<?php

namespace App\Enums;

enum LocationSite: string
{
    case BT = 'BT';
    case JMP1 = 'JMP1';
    case JMP2 = 'JMP2';
    case TGS = 'TGS';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BT => 'Batik Trusmi',
            self::JMP1 => 'JMP 1',
            self::JMP2 => 'JMP 2',
            self::TGS => 'Tegal Sari',
        };
    }
}
