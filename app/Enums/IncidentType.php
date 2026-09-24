<?php

namespace App\Enums;

enum IncidentType: string
{
    case Martyr = 'شهيد';
    case Wounded = 'جريح';
    case Prisoner = 'سجين';
}
