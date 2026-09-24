<?php

namespace App\Actions\SubAgency;

use App\Models\SubAgency;
use Illuminate\Support\Facades\DB;

class UpdateSubAgencyAction
{
    /**
     * Update the sub-agency; when it moves to another agency, its elements move with it.
     *
     * @param  array{name?: string, agency_id?: int}  $attributes
     */
    public function execute(SubAgency $subAgency, array $attributes): SubAgency
    {
        return DB::transaction(function () use ($subAgency, $attributes): SubAgency {
            $subAgency->update($attributes);

            if ($subAgency->wasChanged('agency_id')) {
                $subAgency->elements()->update(['agency_id' => $subAgency->agency_id]);
            }

            return $subAgency;
        });
    }
}
