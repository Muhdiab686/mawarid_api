<?php

namespace App\Console\Commands;

use App\Services\LegacyDataImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

#[Signature('legacy:import
    {--dry-run : Only report how legacy values will be converted, without writing anything}
    {--nullify-unknown : Store NULL for unknown values in nullable enum columns instead of aborting}')]
#[Description('Import the legacy database, converting Arabic enum values to English')]
class ImportLegacyData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(LegacyDataImporter $importer): int
    {
        $enumValues = collect($importer->analyzeEnumValues());

        $this->table(
            ['Column', 'Legacy value', 'Rows', 'New value'],
            $enumValues->map(fn (array $value): array => [
                "{$value['table']}.{$value['column']}",
                $value['legacy_value'] ?? 'NULL',
                $value['rows'],
                match (true) {
                    $value['legacy_value'] === null => 'NULL',
                    $value['is_mapped'] => $value['new_value'],
                    $value['is_nullable'] => '✗ unknown (NULL with --nullify-unknown)',
                    default => '✗ unknown (required column)',
                },
            ])->all(),
        );

        $unmappedValues = $enumValues->reject(fn (array $value): bool => $value['is_mapped']);

        if ($this->option('dry-run')) {
            $this->table(
                ['Table', 'Legacy rows'],
                collect($importer->legacyRowCounts())->map(fn (int $rows, string $table): array => [$table, $rows])->values()->all(),
            );

            if ($nonEmptyTables = $importer->nonEmptyTargetTables()) {
                $this->warn('Target tables must be empty before importing (run `php artisan migrate:fresh`): '.implode(', ', $nonEmptyTables));
            }

            if ($unmappedValues->isNotEmpty()) {
                $this->warn("{$unmappedValues->count()} legacy values ({$unmappedValues->sum('rows')} rows) have no enum mapping yet.");
            } else {
                $this->info('All legacy enum values can be converted.');
            }

            return self::SUCCESS;
        }

        try {
            $importedRows = $importer->import((bool) $this->option('nullify-unknown'));
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Table', 'Imported rows'],
            collect($importedRows)->map(fn (int $rows, string $table): array => [$table, $rows])->values()->all(),
        );

        $this->info('Legacy data imported successfully.');

        return self::SUCCESS;
    }
}
