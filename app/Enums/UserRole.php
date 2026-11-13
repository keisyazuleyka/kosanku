<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Tenant = 'tenant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Owner => 'Pemilik Kost',
            self::Tenant => 'Penyewa',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Owner => 'owner.dashboard',
            self::Tenant => 'tenant.dashboard',
        };
    }
}
