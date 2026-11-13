<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu konfirmasi',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
            self::Completed => 'Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 border border-amber-200/80',
            self::Approved => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
            self::Rejected => 'bg-rose-50 text-rose-700 border border-rose-200/80',
            self::Cancelled => 'bg-zinc-100 text-zinc-600 border border-zinc-200/80',
            self::Completed => 'bg-blue-50 text-blue-700 border border-blue-200/80',
        };
    }
}
