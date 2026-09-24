<?php

namespace App\Enums;

enum IncidentType: string
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
