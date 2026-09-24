<?php

namespace App\Enums;

enum DisciplinaryRecordType: string
{
    case Punishment = 'punishment';
    case Reward = 'reward';
    case Warning = 'warning';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Punishment => 'عقوبة',
            self::Reward => 'مكافأة',
            self::Warning => 'إنذار',
        };
    }
}
