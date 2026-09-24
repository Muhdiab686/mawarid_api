<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Agency extends Model
{
    use SoftDeletes;

    /**
     * @return HasMany<SubAgency, $this>
     */
    public function subAgencies(): HasMany
    {
        return $this->hasMany(SubAgency::class);
    }

    /**
     * @return HasMany<Element, $this>
     */
    public function elements(): HasMany
    {
        return $this->hasMany(Element::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
