<?php

namespace App\Services;

use App\Models\ConsumableStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsumableStockService
{
    /**
     * Create or restore a stock record.
     * Checks for existing product+location combination.
     *
     * @param array $data
     * @return ConsumableStock
     * @throws \Exception
     */
    public function createStock(array $data): ConsumableStock
    {
        return DB::transaction(function () use ($data) {
            try {
                // Check if stock already exists for this product and location
                $existingStock = ConsumableStock::where('product_id', $data['product_id'])
                    ->where('location_id', $data['location_id'])
                    ->first();

                if ($existingStock) {
                    throw new \Exception("Stock for this product at this location already exists. Please update the existing record.");
                }

                return ConsumableStock::create([
                    'product_id' => $data['product_id'],
                    'location_id' => $data['location_id'],
                    'quantity' => $data['quantity'] ?? 0,
                    'min_quantity' => $data['min_quantity'] ?? 0,
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to create consumable stock: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update an existing stock record.
     *
     * @param ConsumableStock $stock
     * @param array $data
     * @return ConsumableStock
     * @throws \Exception
     */
    public function updateStock(ConsumableStock $stock, array $data): ConsumableStock
    {
        return DB::transaction(function () use ($stock, $data) {
            try {
                // Prevent changing product_id or location_id to an existing combination
                // This logic is complex in update, usually we only allow qty updates.
                // But if user changes location, we must check uniqueness.
                if (
                    (isset($data['product_id']) && $data['product_id'] != $stock->product_id) ||
                    (isset($data['location_id']) && $data['location_id'] != $stock->location_id)
                ) {
                    $productId = $data['product_id'] ?? $stock->product_id;
                    $locationId = $data['location_id'] ?? $stock->location_id;

                    $exists = ConsumableStock::where('product_id', $productId)
                        ->where('location_id', $locationId)
                        ->where('id', '!=', $stock->id)
                        ->exists();

                    if ($exists) {
                        throw new \Exception("Another stock record already exists for this product and location.");
                    }
                }

                $stock->update([
                    'product_id' => $data['product_id'] ?? $stock->product_id,
                    'location_id' => $data['location_id'] ?? $stock->location_id,
                    'quantity' => $data['quantity'] ?? $stock->quantity,
                    'min_quantity' => $data['min_quantity'] ?? $stock->min_quantity,
                ]);

                return $stock->fresh();

            } catch (\Exception $e) {
                Log::error("Failed to update consumable stock: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Delete a stock record.
     *
     * @param ConsumableStock $stock
     * @return void
     * @throws \Exception
     */
    public function deleteStock(ConsumableStock $stock): void
    {
        DB::transaction(function () use ($stock) {
            try {
                $stock->delete();
            } catch (\Exception $e) {
                Log::error("Failed to delete consumable stock: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
