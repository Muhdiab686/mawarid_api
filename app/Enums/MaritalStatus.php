<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Widowed = 'widowed';
    case Divorced = 'divorced';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Single => 'أعزب',
            self::Married => 'متزوج/ة',
            self::Widowed => 'أرمل/ة',
            self::Divorced => 'مطلق/ة',
        };
    }
}
