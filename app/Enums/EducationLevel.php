<?php

namespace App\Enums;

enum EducationLevel: string
{
    case Illiterate = 'أمي';
    case Primary = 'ابتدائي';
    case Preparatory = 'إعدادي';
    case Secondary = 'ثانوي';
    case IntermediateInstitute = 'معهد متوسط';
    case University = 'جامعي';
    case Postgraduate = 'دراسات عليا';
}
