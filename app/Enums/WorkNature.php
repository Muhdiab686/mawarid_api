<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum WorkNature: string implements HasLabel
{
    case Administrative = 'administrative';
    case Overnight = 'overnight';
    case ResidentAdministrative = 'resident_administrative';
    case Field = 'field';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Administrative => 'إداري',
            self::Overnight => 'مبيت',
            self::ResidentAdministrative => 'إداري مقيم',
            self::Field => 'ميداني',
        };
    }
}
