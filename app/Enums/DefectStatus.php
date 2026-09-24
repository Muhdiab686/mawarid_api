<?php

namespace App\Enums;

enum DefectStatus: string
{
    case Conscript = 'conscript';
    case Volunteer = 'volunteer';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Conscript => 'مجند',
            self::Volunteer => 'متطوع',
        };
    }
}
