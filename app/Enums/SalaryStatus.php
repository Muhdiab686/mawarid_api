<?php

namespace App\Enums;

enum SalaryStatus: string
{
    case FullSalary = 'full_salary';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::FullSalary => 'راتب كامل',
        };
    }
}
