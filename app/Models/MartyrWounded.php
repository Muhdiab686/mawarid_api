<?php

namespace App\Models;

use App\Enums\IncidentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('martyrs_wounded')]
#[Fillable([
    'element_id',
    'incident_type',
    'incident_date',
    'incident_number',
    'incident_description',
    'documents_paths',
    'notes',
])]
class MartyrWounded extends Model
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
            'incident_type' => IncidentType::class,
            'incident_date' => 'date',
            'documents_paths' => 'array',
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
