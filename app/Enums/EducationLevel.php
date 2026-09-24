<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum EducationLevel: string implements HasLabel
{
    case Illiterate = 'illiterate';
    case Primary = 'primary';
    case Preparatory = 'preparatory';
    case Secondary = 'secondary';
    case IntermediateInstitute = 'intermediate_institute';
    case University = 'university';
    case Postgraduate = 'postgraduate';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Illiterate => 'أمي',
            self::Primary => 'ابتدائي',
            self::Preparatory => 'إعدادي',
            self::Secondary => 'ثانوي',
            self::IntermediateInstitute => 'معهد متوسط',
            self::University => 'جامعي',
            self::Postgraduate => 'دراسات عليا',
        };
    }
}
