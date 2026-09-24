<?php

namespace App\Models;

use App\Enums\EducationLevel;
use App\Enums\ElementStatus;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Nationality;
use App\Enums\WorkNature;
use Database\Factories\ElementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'sub_agency_id',
    'job_role_id',
    'work_location',
    'join_date',
    'military_number',
    'self_number',
    'course_number',
    'full_name',
    'birth_date',
    'grandfather_name',
    'mother_name',
    'nationality',
    'religion',
    'sect',
    'national_id',
    'gender',
    'registry_number',
    'registry_place',
    'residence_place',
    'residence_address',
    'phone_number',
    'marital_status',
    'wives_count',
    'children_count',
    'food_allowance',
    'transport_allowance',
    'is_active',
    'work_nature',
    'bank_account_number',
    'height',
    'weight',
    'skin_color',
    'eye_color',
    'distinguishing_marks',
    'shoe_size',
    'suit_size',
    'education_level',
    'university_major',
    'study_year',
    'cv_summary',
    'experience_summary',
    'academic_courses',
    'military_courses',
    'status',
    'health_status',
    'residence_lat',
    'residence_lng',
    'work_lat',
    'work_lng',
    'distance_km',
])]
class Element extends Model
{
    /** @use HasFactory<ElementFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'birth_date' => 'date',
            'gender' => Gender::class,
            'marital_status' => MaritalStatus::class,
            'work_nature' => WorkNature::class,
            'education_level' => EducationLevel::class,
            'wives_count' => 'integer',
            'children_count' => 'integer',
            'food_allowance' => 'boolean',
            'transport_allowance' => 'boolean',
            'is_active' => 'boolean',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'shoe_size' => 'integer',
            'residence_lat' => 'decimal:7',
            'residence_lng' => 'decimal:7',
            'work_lat' => 'decimal:7',
            'work_lng' => 'decimal:7',
            'distance_km' => 'decimal:2',
            'status' => ElementStatus::class,
            'nationality' => Nationality::class,
        ];
    }

    /**
     * @return BelongsTo<Agency, $this>
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * @return BelongsTo<SubAgency, $this>
     */
    public function subAgency(): BelongsTo
    {
        return $this->belongsTo(SubAgency::class);
    }

    /**
     * @return BelongsTo<JobRole, $this>
     */
    public function jobRole(): BelongsTo
    {
        return $this->belongsTo(JobRole::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<DisciplinaryRecord, $this>
     */
    public function disciplinaryRecords(): HasMany
    {
        return $this->hasMany(DisciplinaryRecord::class);
    }

    /**
     * @return HasMany<ElementRank, $this>
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(ElementRank::class);
    }

    /**
     * @return HasMany<Leave, $this>
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * @return HasMany<MartyrWounded, $this>
     */
    public function martyrWoundedRecords(): HasMany
    {
        return $this->hasMany(MartyrWounded::class);
    }

    /**
     * @return HasMany<MedicalRecord, $this>
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * @return HasMany<Returnee, $this>
     */
    public function returnees(): HasMany
    {
        return $this->hasMany(Returnee::class);
    }

    /**
     * @return HasMany<Telegram, $this>
     */
    public function telegrams(): HasMany
    {
        return $this->hasMany(Telegram::class);
    }

    /**
     * @return HasMany<FuelCard, $this>
     */
    public function fuelCards(): HasMany
    {
        return $this->hasMany(FuelCard::class);
    }

    /**
     * @return HasMany<Furniture, $this>
     */
    public function furnitures(): HasMany
    {
        return $this->hasMany(Furniture::class);
    }

    /**
     * @return HasMany<Technology, $this>
     */
    public function technologies(): HasMany
    {
        return $this->hasMany(Technology::class);
    }

    /**
     * @return HasMany<Vehicle, $this>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * @return HasMany<Weapon, $this>
     */
    public function weapons(): HasMany
    {
        return $this->hasMany(Weapon::class);
    }
}
