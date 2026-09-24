<?php

namespace App\Models;

use App\Enums\DefectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'element_id',
    'status_at_defect',
    'defect_date',
    'regime_volunteer_date',
    'rank_at_defect',
    'id_at_defect',
    'defect_agency',
    'last_place_before_defect',
    'last_promotion_degree',
    'last_promotion_date',
    'hts_volunteer_date',
    'hts_old_workplace',
    'ssg_interior_volunteer_date',
    'ssg_military_number',
    'ssg_old_workplace',
    'sig_police_volunteer_date',
    'sig_old_workplace',
    'return_date',
    'rank_after_return',
    'notes',
])]
class Returnee extends Model
{
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_at_defect' => DefectStatus::class,
            'defect_date' => 'date',
            'regime_volunteer_date' => 'date',
            'last_promotion_date' => 'date',
            'hts_volunteer_date' => 'date',
            'ssg_interior_volunteer_date' => 'date',
            'sig_police_volunteer_date' => 'date',
            'return_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Element, $this>
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }
}
