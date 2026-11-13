<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Menunggu verifikasi',
            self::Approved => 'Aktif',
            self::Rejected => 'Ditolak',
            self::Suspended => 'Ditangguhkan',
            self::Archived => 'Diarsipkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-zinc-100 text-zinc-700 border border-zinc-200/80',
            self::Pending => 'bg-amber-50 text-amber-700 border border-amber-200/80',
            self::Approved => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
            self::Rejected => 'bg-rose-50 text-rose-700 border border-rose-200/80',
            self::Suspended => 'bg-zinc-100 text-zinc-600 border border-zinc-200/80',
            self::Archived => 'bg-slate-100 text-slate-600 border border-slate-200/80',
        };
    }
}
