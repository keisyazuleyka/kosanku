<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Reviewing = 'reviewing';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Reviewing => 'Diproses',
            self::Resolved => 'Selesai',
            self::Dismissed => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 border border-amber-200/80',
            self::Reviewing => 'bg-blue-50 text-blue-700 border border-blue-200/80',
            self::Resolved => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
            self::Dismissed => 'bg-zinc-100 text-zinc-600 border border-zinc-200/80',
        };
    }
}
