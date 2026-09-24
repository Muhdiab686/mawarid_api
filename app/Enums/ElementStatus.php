<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum ElementStatus: string implements HasLabel
{
    case Active = 'active';
    case ContractedCivilian = 'contracted_civilian';
    case DesertionTelegram = 'desertion_telegram';
    case Dismissed = 'dismissed';
    case SickLeave = 'sick_leave';
    case ExternalTransfer = 'external_transfer';
    case WorkSuspension = 'work_suspension';
    case SuspendedFromDuty = 'suspended_from_duty';
    case Imprisoned = 'imprisoned';
    case AdministrativeLeave = 'administrative_leave';
    case Martyr = 'martyr';
    case Resigned = 'resigned';
    case ChronicallyIll = 'chronically_ill';
    case Wounded = 'wounded';
    case CrimeTelegram = 'crime_telegram';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::ContractedCivilian => 'مدني متعاقد',
            self::DesertionTelegram => 'برقية فرار',
            self::Dismissed => 'مفصول',
            self::SickLeave => 'إجازة صحية',
            self::ExternalTransfer => 'نقل خارجي',
            self::WorkSuspension => 'إيقاف عمل',
            self::SuspendedFromDuty => 'كف يد',
            self::Imprisoned => 'سجن',
            self::AdministrativeLeave => 'إجازة إدارية',
            self::Martyr => 'شهيد',
            self::Resigned => 'استقالة',
            self::ChronicallyIll => 'مريض مزمن',
            self::Wounded => 'جريح',
            self::CrimeTelegram => 'برقية جرم',
        };
    }
}
