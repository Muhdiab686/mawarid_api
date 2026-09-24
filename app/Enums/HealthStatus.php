<?php

namespace App\Enums;

enum HealthStatus: string
{
    case Healthy = 'healthy';
    case TransientIllness = 'transient_illness';
    case ChronicIllness = 'chronic_illness';
    case WorkInjury = 'work_injury';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Healthy => 'سليم',
            self::TransientIllness => 'مرض عابر',
            self::ChronicIllness => 'مرض مزمن',
            self::WorkInjury => 'إصابة عمل',
        };
    }
}
