<?php

namespace App\Observers;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Support\Str;
use App\Models\InstalledItem;
use App\Models\InstalledItemHistory;
use Illuminate\Validation\ValidationException;

class InstalledItemObserver
{
    /**
     * Handle the InstalledItem "creating" event.
     * Validates the parent item type and generates a unique code
     * before the record is persisted to the database.
     *
     * @param  InstalledItem  $instance
     * @return void
     * @throws ValidationException
     */
    public function creating(InstalledItem $instance): void
    {
        // 1. Validate Parent Item Integrity
        $item = $instance->item ?? Item::find($instance->item_id);

        if (!$item || $item->type !== ItemType::Installed) {
            throw ValidationException::withMessages([
                'item_id' => 'Item tidak valid atau bukan tipe Installed (Terpasang).'
            ]);
        }

        // 2. Auto-Generate Unique Code
        // Format: [PREFIX]-[YYMMDD]-[RANDOM]
        if (empty($instance->code)) {
            $prefix = $item->code;
            $dateCode = now()->format('ymd');

            // Loop to ensure the code is truly unique (Collision Check)
            do {
                $randomSuffix = strtoupper(Str::random(4));
                $fullCode = "{$prefix}-{$dateCode}-{$randomSuffix}";
            } while (InstalledItem::where('code', $fullCode)->exists());

            $instance->code = $fullCode;
        }
    }

    /**
     * Handle the InstalledItem "saving" event.
     * Performs additional validation during updates to ensure
     * data consistency before changes are saved.
     *
     * @param  InstalledItem  $instance
     * @return void
     * @throws ValidationException
     */
    public function saving(InstalledItem $instance): void
    {
        // Validate location changes (only allowed for Installed items)
        if ($instance->exists && $instance->isDirty('location_id')) {
            if (!$instance->relationLoaded('item')) {
                $instance->load('item');
            }

            // Safety check: Ensure type consistency
            if ($instance->item->type !== ItemType::Installed) {
                throw ValidationException::withMessages([
                    'item_id' => 'Hanya Barang tipe Terpasang yang boleh dipindahkan lokasinya.',
                ]);
            }
        }
    }

    /**
     * Handle the InstalledItem "saved" event.
     * Triggered after the record is successfully saved or updated.
     * Manages history logging for initial installation and location transfers.
     *
     * @param  InstalledItem  $instance
     * @return void
     */
    public function saved(InstalledItem $instance): void
    {
        // CASE 1: Newly Created (Initial Installation)
        if ($instance->wasRecentlyCreated) {
            InstalledItemHistory::create([
                'installed_item_id' => $instance->id,
                'location_id' => $instance->location_id,
                'installed_at' => $instance->installed_at,
                'notes' => 'Pemasangan awal',
            ]);
        }
        // CASE 2: Location Changed (Transfer/Mutation)
        elseif ($instance->isDirty('location_id')) {
            $movementDate = $instance->installed_at;

            // 1. Close the previous history record (Set removed_at)
            // Finds the latest active history record
            InstalledItemHistory::where('installed_item_id', $instance->id)
                ->whereNull('removed_at')
                ->latest('installed_at')
                ->first()
                    ?->update(['removed_at' => $movementDate]);

            // 2. Create a new history record for the current location
            InstalledItemHistory::create([
                'installed_item_id' => $instance->id,
                'location_id' => $instance->location_id,
                'installed_at' => $movementDate,
                'notes' => 'Pindah lokasi (Mutasi)',
            ]);
        }
    }

    /**
     * Handle the InstalledItem "deleted" event.
     * Closes open history logs when the item is soft-deleted.
     *
     * @param  InstalledItem  $instance
     * @return void
     */
    public function deleted(InstalledItem $instance): void
    {
        // Close history log upon deletion
        InstalledItemHistory::where('installed_item_id', $instance->id)
            ->whereNull('removed_at')
            ->update([
                'removed_at' => now(),
                'notes' => 'Barang dinonaktifkan/dihapus dari sistem'
            ]);
    }
}
