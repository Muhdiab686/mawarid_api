<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'element_id',
    'telegram_number',
    'telegram_date',
    'telegram_type',
    'old_agency_id',
    'old_sub_agency_id',
    'new_agency_id',
    'new_sub_agency_id',
    'document_path',
    'notes',
    'additional_notes',
])]
class Telegram extends Model
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
            'telegram_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Element, $this>
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    /**
     * @return BelongsTo<Agency, $this>
     */
    public function oldAgency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'old_agency_id');
    }

    /**
     * @return BelongsTo<SubAgency, $this>
     */
    public function oldSubAgency(): BelongsTo
    {
        return $this->belongsTo(SubAgency::class, 'old_sub_agency_id');
    }

    /**
     * @return BelongsTo<Agency, $this>
     */
    public function newAgency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'new_agency_id');
    }

    /**
     * @return BelongsTo<SubAgency, $this>
     */
    public function newSubAgency(): BelongsTo
    {
        return $this->belongsTo(SubAgency::class, 'new_sub_agency_id');
    }
}
