<?php

namespace App\Services;

use Throwable;
use App\Models\Asset;
use App\DTOs\AssetData;
use App\Models\AssetHistory;
use App\Enums\AssetStatus;
use App\Exceptions\AssetException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetService
{
    /**
     * Create a new asset and log the initial history.
     */
    public function createAsset(AssetData $data): Asset
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Generate Asset Tag if not present
                $assetTag = $data->asset_tag ?? $this->generateAssetTag();

                // 2. Create the Asset
                $asset = Asset::create([
                    'product_id' => $data->product_id,
                    'location_id' => $data->location_id,
                    'asset_tag' => $assetTag,
                    'serial_number' => $data->serial_number,
                    'status' => $data->status ?? AssetStatus::InStock,
                    'purchase_date' => $data->purchase_date,
                    'image_path' => $data->image_path,
                    'notes' => $data->notes,
                ]);

                // 3. Log Initial History
                $this->logHistory(
                    asset: $asset,
                    actionType: 'checkin',
                    notes: 'Initial asset registration',
                    newLocationId: $asset->location_id,
                    newStatus: $asset->status
                );

                return $asset;

            } catch (Throwable $e) {
                throw AssetException::createFailed($e->getMessage(), $e);
            }
        });
    }

    /**
     * Update an asset and log history if critical fields change.
     */
    public function updateAsset(Asset $asset, AssetData $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            try {
                // Lock the record to prevent race conditions during update
                // This ensures that if two admins try to move/update at the same time,
                // one will block until the other is done or fail if logic conflicts.
                $asset->refresh()->lockForUpdate();

                $oldStatus = $asset->status;
                $oldLocationId = $asset->location_id;

                $asset->update($data->toArray());

                // Detect changes
                $newStatus = $asset->status;
                $newLocationId = $asset->location_id; // DTO might have updated it

                if ($oldStatus !== $newStatus || $oldLocationId !== $newLocationId) {
                    $actionType = 'update';
                    if ($oldStatus !== $newStatus)
                        $actionType = 'status_change';
                    elseif ($oldLocationId !== $newLocationId)
                        $actionType = 'movement';

                    $this->logHistory(
                        asset: $asset,
                        actionType: $actionType,
                        notes: $data->history_notes ?? 'Asset updated',
                        recipientName: $data->recipient_name,
                        newLocationId: $newLocationId,
                        newStatus: $newStatus
                    );
                }

                return $asset->refresh();

            } catch (Throwable $e) {
                throw AssetException::updateFailed((string) $asset->id, $e->getMessage(), $e);
            }
        });
    }

    /**
     * Delete an asset.
     */
    public function deleteAsset(Asset $asset): void
    {
        DB::transaction(function () use ($asset) {
            try {
                $asset->histories()->delete();
                $asset->delete();
            } catch (Throwable $e) {
                throw AssetException::deletionFailed((string) $asset->id, $e->getMessage(), $e);
            }
        });
    }

    /**
     * Helper to log history safely.
     */
    private function logHistory(
        Asset $asset,
        string $actionType,
        string $notes,
        ?int $newLocationId = null,
        ?AssetStatus $newStatus = null,
        ?string $recipientName = null
    ): void {
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'location_id' => $newLocationId ?? $asset->location_id,
            'status' => $newStatus ?? $asset->status,
            'recipient_name' => $recipientName,
            'action_type' => $actionType,
            'notes' => $notes,
        ]);
    }

    /**
     * Generate a unique asset tag.
     */
    private function generateAssetTag(): string
    {
        do {
            $randomCode = strtoupper(\Illuminate\Support\Str::random(4));
            $dateCode = date('ymd');
            $tag = "INV.{$randomCode}.{$dateCode}";
        } while (Asset::where('asset_tag', $tag)->exists());

        return $tag;
    }
}
