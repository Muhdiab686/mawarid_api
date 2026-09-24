<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case Single = 'أعزب';
    case Married = 'متزوج/ة';
    case Widowed = 'أرمل/ة';
    case Divorced = 'مطلق/ة';
}
