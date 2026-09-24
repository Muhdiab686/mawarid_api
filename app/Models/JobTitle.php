<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title'])]
class JobTitle extends Model
{
    use SoftDeletes;

    /**
     * @return HasMany<JobRole, $this>
     */
    public function jobRoles(): HasMany
    {
        return $this->hasMany(JobRole::class);
    }
}
