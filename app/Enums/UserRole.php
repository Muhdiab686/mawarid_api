<?php

namespace App\Enums;

use App\Interfaces\HasLabel;

enum UserRole: string implements HasLabel
{
    case SuperAdmin = 'super_admin';
    case SeniorAdmin = 'senior_admin';
    case TelegramAdmin = 'telegram_admin';
    case AttendanceAdmin = 'attendance_admin';
    case MartyrsAdmin = 'martyrs_admin';
    case MedicalAdmin = 'medical_admin';
    case LogisticsAdmin = 'logistics_admin';
    case DashboardViewer = 'dashboard_viewer';
    case AgencyAdmin = 'agency_admin';

    /**
     * Get the permissions granted to this role.
     *
     * Only the senior administration has access for now; the other roles
     * receive their permissions in the project's final phase.
     *
     * @return list<Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SeniorAdmin => Permission::cases(),
            self::SuperAdmin => [],
            self::TelegramAdmin => [],
            self::AttendanceAdmin => [],
            self::MartyrsAdmin => [],
            self::MedicalAdmin => [],
            self::LogisticsAdmin => [],
            self::DashboardViewer => [],
            self::AgencyAdmin => [],
        };
    }

    /**
     * Get the Arabic label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'مدير النظام',
            self::SeniorAdmin => 'إدارة عليا',
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
