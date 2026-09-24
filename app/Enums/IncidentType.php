<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum IncidentType: string implements HasLabel
{
    case Martyr = 'martyr';
    case Wounded = 'wounded';
    case Prisoner = 'prisoner';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Martyr => 'شهيد',
            self::Wounded => 'جريح',
            self::Prisoner => 'سجين',
        };
    }
}
