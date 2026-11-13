<?php

namespace App\Enums;

enum PropertyType: string
{
    case Putra = 'putra';
    case Putri = 'putri';
    case Campur = 'campur';

    public function label(): string
    {
        return match ($this) {
            self::Putra => 'Kost Putra',
            self::Putri => 'Kost Putri',
            self::Campur => 'Kost Campur',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Putra => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::Putri => 'bg-pink-50 text-pink-700 border border-pink-200',
            self::Campur => 'bg-purple-50 text-purple-700 border border-purple-200',
        };
    }
}
