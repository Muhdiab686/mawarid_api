<?php

namespace App\Enums;

enum LeaveType: string
{
    case Medical = 'إجازة طبية';
    case Administrative = 'إجازة إدارية';
    case Unpaid = 'اجازة بدون راتب';
    case Study = 'اجازة دراسية';
    case External = 'اجازة خارجية';
    case Maternity = 'اجازة امومة';
}
