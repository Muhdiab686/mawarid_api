<?php

namespace App\Enums;

enum DisciplinaryRecordType: string
{
    case Punishment = 'عقوبة';
    case Reward = 'مكافأة';
    case Warning = 'إنذار';
}
