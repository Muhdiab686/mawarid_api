<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'element_id',
    'category_type',
    'readiness_status',
    'asset_name',
    'manufacturer',
    'origin_country',
    'model',
    'manufacturing_year',
    'source',
    'evaluation',
    'origin_number',
    'color',
    'cpu_type',
    'ram_size',
    'storage_details',
    'screen_size',
    'gpu_details',
    'accessories',
    'notes',
    'handover_date',
])]
class Technology extends Model
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
            'handover_date' => 'date',
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
