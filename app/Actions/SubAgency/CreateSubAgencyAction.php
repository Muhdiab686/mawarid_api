<?php

namespace App\Actions\SubAgency;

use App\Models\Agency;
use App\Models\SubAgency;

class CreateSubAgencyAction
{
    /**
     * @param  array{name: string}  $attributes
     */
    public function execute(Agency $agency, array $attributes): SubAgency
    {
        return $agency->subAgencies()->create($attributes);
    }
}
