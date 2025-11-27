<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AreaCategory: string implements HasLabel, HasColor
{
    case Housing = 'housing';
    case Office = 'office';
    case Store = 'store';
    case Warehouse = 'warehouse';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Housing => 'Perumahan',
            self::Office => 'Kantor',
            self::Store => 'Toko / Store',
            self::Warehouse => 'Gudang',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Housing => 'info',
            self::Office => 'success',
            self::Store => 'warning',
            self::Warehouse => 'gray',
        };
    }
}
