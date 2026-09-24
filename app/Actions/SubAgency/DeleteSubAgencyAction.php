<?php

namespace App\Actions\SubAgency;

use App\Models\SubAgency;
use Illuminate\Validation\ValidationException;

class DeleteSubAgencyAction
{
    /**
     * Soft delete the sub-agency.
     *
     * @throws ValidationException When elements are still linked to the sub-agency.
     */
    public function execute(SubAgency $subAgency): void
    {
        if ($subAgency->elements()->exists()) {
            throw ValidationException::withMessages([
                'sub_agency' => 'لا يمكن حذف الجهة الفرعية لوجود عناصر مرتبطة بها.',
            ]);
        }

        $subAgency->delete();
    }
}
