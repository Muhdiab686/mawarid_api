<?php

namespace App\Enums;

enum ElementStatus: string
{
    case Active = 'active';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
        };
    }
}
