<?php

namespace App\Observers;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Support\Str;
use App\Models\InstalledItemInstance;
use App\Models\InstalledItemLocationHistory;
use Illuminate\Validation\ValidationException;

class InstalledItemInstanceObserver
{
    public function creating(InstalledItemInstance $instance): void
    {
        $item = $instance->item ?? Item::find($instance->item_id);

        if (!$item || $item->type !== ItemType::Installed) {
            throw ValidationException::withMessages(['item_id' => 'Item tidak valid.']);
        }

        if (empty($instance->code)) {
            $prefix = $item->code;

            $dateCode = now()->format('ymd');
            $randomSuffix = strtoupper(Str::random(4));

            $instance->code = "{$prefix}-{$dateCode}-{$randomSuffix}";
        }
    }

    public function saving(InstalledItemInstance $instance): void
    {
        if ($instance->exists && $instance->isDirty('current_location_id')) {
            if (!$instance->relationLoaded('item')) {
                $instance->load('item');
            }
            if ($instance->item->type !== ItemType::Installed) {
                throw ValidationException::withMessages([
                    'item_id' => 'Hanya Barang tipe Terpasang yang boleh dipindahkan lokasinya.',
                ]);
            }
        }
    }

    public function saved(InstalledItemInstance $instance): void
    {
        if ($instance->wasRecentlyCreated) {
            InstalledItemLocationHistory::create([
                'instance_id' => $instance->id,
                'location_id' => $instance->current_location_id,
                'installed_at' => $instance->installed_at,
                'notes' => 'Pemasangan awal',
            ]);
        } elseif ($instance->isDirty('current_location_id')) {
            $movementDate = $instance->installed_at;

            InstalledItemLocationHistory::where('instance_id', $instance->id)
                ->whereNull('removed_at')
                ->update(['removed_at' => $movementDate]);

            InstalledItemLocationHistory::create([
                'instance_id' => $instance->id,
                'location_id' => $instance->current_location_id,
                'installed_at' => $movementDate,
                'notes' => 'Pindah lokasi',
            ]);
        }
    }

    public function deleted(InstalledItemInstance $instance): void
    {
        InstalledItemLocationHistory::where('instance_id', $instance->id)
            ->whereNull('removed_at')
            ->update([
                'removed_at' => now(),
                'notes' => 'Barang dinonaktifkan/dihapus dari sistem'
            ]);
    }
}
