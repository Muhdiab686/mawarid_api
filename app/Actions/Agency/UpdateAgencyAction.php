<?php

namespace App\Actions\Agency;

use App\Models\Agency;

class UpdateAgencyAction
{
    /**
     * @param  array{name?: string}  $attributes
     */
    public function execute(Agency $agency, array $attributes): Agency
    {
        $agency->update($attributes);

        return $agency;
    }
}
