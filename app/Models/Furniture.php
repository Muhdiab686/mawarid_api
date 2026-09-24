<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('furnitures')]
#[Fillable([
    'element_id',
    'level_2',
    'level_3',
    'level_4',
    'category_type',
    'asset_name',
    'current_value',
    'technical_condition',
    'color',
    'manufacturer',
    'model',
    'quantity',
    'origin_country',
    'dimensions',
    'notes',
    'handover_date',
])]
class Furniture extends Model
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
            'current_value' => 'decimal:2',
            'quantity' => 'integer',
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
