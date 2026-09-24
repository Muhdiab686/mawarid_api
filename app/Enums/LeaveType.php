<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum LeaveType: string implements HasLabel
{
    case Medical = 'medical';
    case Administrative = 'administrative';
    case Unpaid = 'unpaid';
    case Study = 'study';
    case External = 'external';
    case Maternity = 'maternity';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Medical => 'إجازة طبية',
            self::Administrative => 'إجازة إدارية',
            self::Unpaid => 'إجازة بدون راتب',
            self::Study => 'إجازة دراسية',
            self::External => 'إجازة خارجية',
            self::Maternity => 'إجازة أمومة',
        };
    }
}
