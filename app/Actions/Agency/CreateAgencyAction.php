<?php

namespace App\Actions\Agency;

use App\Models\Agency;

class CreateAgencyAction
{
    /**
     * @param  array{name: string}  $attributes
     */
    public function execute(array $attributes): Agency
    {
        return Agency::create($attributes);
    }
}
