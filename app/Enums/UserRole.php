<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case TelegramAdmin = 'telegram_admin';
    case AttendanceAdmin = 'attendance_admin';
    case MartyrsAdmin = 'martyrs_admin';
    case MedicalAdmin = 'medical_admin';
    case LogisticsAdmin = 'logistics_admin';
    case DashboardViewer = 'dashboard_viewer';
    case AgencyAdmin = 'agency_admin';

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'مدير النظام',
            self::TelegramAdmin => 'مسؤول البرقيات',
            self::AttendanceAdmin => 'مسؤول الدوام',
            self::MartyrsAdmin => 'مسؤول الشهداء والجرحى',
            self::MedicalAdmin => 'مسؤول الشؤون الطبية',
            self::LogisticsAdmin => 'مسؤول الإمداد',
            self::DashboardViewer => 'مشاهد لوحة التحكم',
            self::AgencyAdmin => 'مسؤول الجهة',
        };
    }
}
