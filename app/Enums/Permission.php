<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum Permission: string implements HasLabel
{
    case ViewAgencies = 'agencies.view';
    case CreateAgencies = 'agencies.create';
    case UpdateAgencies = 'agencies.update';
    case DeleteAgencies = 'agencies.delete';
    case ViewSubAgencies = 'sub_agencies.view';
    case CreateSubAgencies = 'sub_agencies.create';
    case UpdateSubAgencies = 'sub_agencies.update';
    case DeleteSubAgencies = 'sub_agencies.delete';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::ViewAgencies => 'عرض الجهات',
            self::CreateAgencies => 'إضافة جهة',
            self::UpdateAgencies => 'تعديل جهة',
            self::DeleteAgencies => 'حذف جهة',
            self::ViewSubAgencies => 'عرض الجهات الفرعية',
            self::CreateSubAgencies => 'إضافة جهة فرعية',
            self::UpdateSubAgencies => 'تعديل جهة فرعية',
            self::DeleteSubAgencies => 'حذف جهة فرعية',
        };
    }
}
