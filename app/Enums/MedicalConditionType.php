<?php

namespace App\Enums;

enum MedicalConditionType: string
{
    case Healthy = 'سليم';
    case TransientIllness = 'مرض عابر';
    case ChronicIllness = 'مرض مزمن';
    case WorkInjury = 'إصابة عمل';
}
