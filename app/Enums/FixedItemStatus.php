<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FixedItemStatus: string implements HasLabel, HasColor
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Maintenance = 'maintenance';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Borrowed => 'Dipinjam',
            self::Maintenance => 'Perawatan / Rusak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Available => 'success',
            self::Borrowed => 'warning',
            self::Maintenance => 'danger',
        };
    }
}
