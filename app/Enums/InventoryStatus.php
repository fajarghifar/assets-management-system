<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum InventoryStatus: string implements HasLabel, HasColor
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Maintenance = 'maintenance';
    case Broken = 'broken';
    case Lost = 'lost';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Borrowed => 'Dipinjam',
            self::Maintenance => 'Dalam Perbaikan',
            self::Broken => 'Rusak',
            self::Lost => 'Hilang',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Available => 'success',
            self::Borrowed => 'info',
            self::Maintenance => 'warning',
            self::Broken, self::Lost => 'danger',
        };
    }
}
