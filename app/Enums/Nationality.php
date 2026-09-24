<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum Nationality: string implements HasLabel
{
    case Syrian = 'syrian';
    case PalestinianSyrian = 'palestinian_syrian';
    case Palestinian = 'palestinian';
    case Jordanian = 'jordanian';
    case Tunisian = 'tunisian';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Syrian => 'سوري',
            self::PalestinianSyrian => 'فلسطيني/سوري',
            self::Palestinian => 'فلسطيني',
            self::Jordanian => 'أردني',
            self::Tunisian => 'تونسي',
        };
    }
}
