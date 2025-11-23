<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Observers\ItemObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(ItemObserver::class)]
class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'description'
    ];

    protected $casts = [
        'type' => ItemType::class,
    ];

    public function stocks()
    {
        return $this->hasMany(ItemStock::class);
    }

    public function fixedInstances()
    {
        return $this->hasMany(FixedItemInstance::class);
    }

    public function installedInstances()
    {
        return $this->hasMany(InstalledItemInstance::class);
    }

    public function ensureCanBeDeleted(): void
    {
        $canDelete = match ($this->type) {
            ItemType::Consumable => $this->stocks()->sum('quantity') === 0,
            ItemType::Fixed => $this->fixedInstances()->where('status', '!=', 'available')->doesntExist(),
            ItemType::Installed => true,
        };

        if (!$canDelete) {
            $msg = match ($this->type) {
                ItemType::Consumable => 'Masih ada sisa stok fisik yang tercatat.',
                ItemType::Fixed => 'Masih ada unit asset yang sedang dipinjam atau dalam perbaikan.',
                default => 'Item sedang digunakan dalam sistem.',
            };

            throw ValidationException::withMessages([
                'item' => "Penghapusan Ditolak: {$msg}"
            ]);
        }
    }
}
