<?php

namespace App\Actions\Agency;

use App\Models\Agency;
use App\Models\Element;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteAgencyAction
{
    /**
     * Soft delete the agency together with its sub-agencies.
     *
     * @throws ValidationException When elements are still linked to the agency or its sub-agencies.
     */
    public function execute(Agency $agency): void
    {
        $hasLinkedElements = Element::query()
            ->where('agency_id', $agency->id)
            ->orWhereIn('sub_agency_id', $agency->subAgencies()->select('id'))
            ->exists();

        if ($hasLinkedElements) {
            throw ValidationException::withMessages([
                'agency' => 'لا يمكن حذف الجهة لوجود عناصر مرتبطة بها أو بجهاتها الفرعية.',
            ]);
        }

        DB::transaction(function () use ($agency): void {
            $agency->subAgencies()->delete();
            $agency->delete();
        });
    }
}
