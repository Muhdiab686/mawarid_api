<?php

namespace App\Enums;

enum WorkNature: string
{
    case Administrative = 'إداري';
    case Overnight = 'مبيت';
    case ResidentAdministrative = 'إداري مقيم';
    case Field = 'ميداني';
}
