<?php

namespace App\Services;

use App\Enums\DefectStatus;
use App\Enums\DisciplinaryRecordType;
use App\Enums\EducationLevel;
use App\Enums\ElementStatus;
use App\Enums\Gender;
use App\Enums\HealthStatus;
use App\Enums\IncidentType;
use App\Enums\LeaveType;
use App\Enums\MaritalStatus;
use App\Enums\Nationality;
use App\Enums\SalaryStatus;
use App\Enums\UserRole;
use App\Enums\WorkNature;
use App\Interfaces\HasLabel;
use BackedEnum;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Copies the legacy (Arabic-enum) database into the new schema, converting
 * Arabic enum values to their English enum values and preserving primary keys.
 */
class LegacyDataImporter
{
    public const LEGACY_CONNECTION = 'legacy';

    private const CHUNK_SIZE = 500;

    /**
     * Domain tables copied from the legacy database, in foreign-key order.
     *
     * @var list<string>
     */
    public const TABLES = [
        'agencies',
        'sub_agencies',
        'job_titles',
        'job_roles',
        'users',
        'elements',
        'attendances',
        'disciplinary_records',
        'element_ranks',
        'leaves',
        'martyrs_wounded',
        'medical_records',
        'returnees',
        'telegrams',
        'fuel_cards',
        'furnitures',
        'technologies',
        'vehicles',
        'weapons',
        'audit_logs',
    ];

    /**
     * Columns whose legacy Arabic values are converted to English enum values.
     *
     * @var array<string, array<string, class-string<BackedEnum&HasLabel>>>
     */
    public const ENUM_COLUMNS = [
        'users' => ['role_type' => UserRole::class],
        'elements' => [
            'gender' => Gender::class,
            'marital_status' => MaritalStatus::class,
            'work_nature' => WorkNature::class,
            'education_level' => EducationLevel::class,
            'status' => ElementStatus::class,
            'nationality' => Nationality::class,
        ],
        'attendances' => ['salary_status' => SalaryStatus::class],
        'disciplinary_records' => ['record_type' => DisciplinaryRecordType::class],
        'leaves' => ['leave_type' => LeaveType::class],
        'martyrs_wounded' => ['incident_type' => IncidentType::class],
        'medical_records' => ['condition_type' => HealthStatus::class],
        'returnees' => ['status_at_defect' => DefectStatus::class],
    ];

    /**
     * Legacy values that match no enum label, mapped explicitly to an enum value.
     *
     * @var array<string, array<string, array<string, string>>>
     */
    public const VALUE_ALIASES = [
        'elements' => [
            'nationality' => ['فلسطيني سوري' => 'palestinian_syrian'],
        ],
    ];

    /**
     * Summarise every distinct legacy enum value and what it converts to.
     *
     * @return list<array{table: string, column: string, legacy_value: string|null, rows: int, new_value: string|null, is_mapped: bool, is_nullable: bool}>
     */
    public function analyzeEnumValues(): array
    {
        $summary = [];

        foreach (self::ENUM_COLUMNS as $table => $columns) {
            foreach ($columns as $column => $enumClass) {
                $isNullable = $this->isNullableInTarget($table, $column);

                $distinctValues = $this->legacy()->table($table)
                    ->select($column)
                    ->selectRaw('count(*) as aggregate')
                    ->groupBy($column)
                    ->orderByDesc('aggregate')
                    ->get();

                foreach ($distinctValues as $distinctValue) {
                    $legacyValue = $distinctValue->{$column};
                    $newValue = $legacyValue === null ? null : $this->convertEnumValue($table, $column, $legacyValue);

                    $summary[] = [
                        'table' => $table,
                        'column' => $column,
                        'legacy_value' => $legacyValue,
                        'rows' => (int) $distinctValue->aggregate,
                        'new_value' => $newValue,
                        'is_mapped' => $legacyValue === null || $newValue !== null,
                        'is_nullable' => $isNullable,
                    ];
                }
            }
        }

        return $summary;
    }

    /**
     * Count the rows of each legacy table.
     *
     * @return array<string, int>
     */
    public function legacyRowCounts(): array
    {
        return collect(self::TABLES)
            ->mapWithKeys(fn (string $table): array => [$table => $this->legacy()->table($table)->count()])
            ->all();
    }

    /**
     * List the target tables that already contain rows.
     *
     * @return list<string>
     */
    public function nonEmptyTargetTables(): array
    {
        return array_values(array_filter(
            self::TABLES,
            fn (string $table): bool => DB::table($table)->exists(),
        ));
    }

    /**
     * Copy every legacy table into the new database inside one transaction.
     *
     * @return array<string, int> Imported row count per table.
     *
     * @throws RuntimeException When the target is not empty or a value cannot be converted.
     */
    public function import(bool $nullifyUnknownValues = false): array
    {
        $blockingValues = collect($this->analyzeEnumValues())
            ->reject(fn (array $value): bool => $value['is_mapped'])
            ->reject(fn (array $value): bool => $nullifyUnknownValues && $value['is_nullable']);

        if ($blockingValues->isNotEmpty()) {
            throw new RuntimeException('Unmapped legacy values: '.$blockingValues
                ->map(fn (array $value): string => "{$value['table']}.{$value['column']} = \"{$value['legacy_value']}\"")
                ->implode(', '));
        }

        if ($nonEmptyTables = $this->nonEmptyTargetTables()) {
            throw new RuntimeException('Target tables are not empty: '.implode(', ', $nonEmptyTables));
        }

        return DB::transaction(fn (): array => collect(self::TABLES)
            ->mapWithKeys(fn (string $table): array => [$table => $this->copyTable($table)])
            ->all());
    }

    /**
     * Convert a legacy value to its English enum value, or null when it matches no case.
     */
    public function convertEnumValue(string $table, string $column, string $legacyValue): ?string
    {
        if (isset(self::VALUE_ALIASES[$table][$column][$legacyValue])) {
            return self::VALUE_ALIASES[$table][$column][$legacyValue];
        }

        $enumClass = self::ENUM_COLUMNS[$table][$column];

        if ($case = $enumClass::tryFrom($legacyValue)) {
            return $case->value;
        }

        $normalizedValue = $this->normalizeArabic($legacyValue);

        foreach ($enumClass::cases() as $case) {
            if ($this->normalizeArabic($case->label()) === $normalizedValue) {
                return $case->value;
            }
        }

        return null;
    }

    private function copyTable(string $table): int
    {
        $sharedColumns = array_values(array_intersect(
            Schema::connection(self::LEGACY_CONNECTION)->getColumnListing($table),
            Schema::getColumnListing($table),
        ));

        $copiedRows = 0;

        $this->legacy()->table($table)
            ->select($sharedColumns)
            ->chunkById(self::CHUNK_SIZE, function (Collection $rows) use ($table, &$copiedRows): void {
                $records = $rows->map(fn (object $row): array => $this->convertRow($table, (array) $row))->all();

                DB::table($table)->insert($records);

                $copiedRows += count($records);
            });

        return $copiedRows;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function convertRow(string $table, array $row): array
    {
        foreach (array_keys(self::ENUM_COLUMNS[$table] ?? []) as $column) {
            if ($row[$column] !== null) {
                $row[$column] = $this->convertEnumValue($table, $column, $row[$column]);
            }
        }

        return $row;
    }

    /**
     * Unify hamza/taa-marbuta/alef-maqsura spellings and whitespace so
     * "اجازة امومة" matches "إجازة أمومة".
     */
    private function normalizeArabic(string $value): string
    {
        $value = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $value);

        return (string) preg_replace('/\s+/u', ' ', trim($value));
    }

    private function isNullableInTarget(string $table, string $column): bool
    {
        $definition = collect(Schema::getColumns($table))->firstWhere('name', $column);

        return (bool) ($definition['nullable'] ?? false);
    }

    private function legacy(): Connection
    {
        return DB::connection(self::LEGACY_CONNECTION);
    }
}
