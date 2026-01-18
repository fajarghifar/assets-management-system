<?php

namespace App\Services;

use App\DTOs\ProductData;
use App\Models\Product;
use App\Exceptions\ProductException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    /**
     * Create a new product.
     */
    public function createProduct(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                return Product::create($data->toArray());
            } catch (Throwable $e) {
                throw ProductException::createFailed($e->getMessage(), $e);
            }
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, ProductData $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            try {
                $product->update($data->toArray());
                return $product->refresh();
            } catch (Throwable $e) {
                throw ProductException::updateFailed((string) $product->id, $e->getMessage(), $e);
            }
        });
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            try {
                if ($product->assets()->exists()) {
                    throw ProductException::inUse("Cannot delete product '{$product->name}' because it has associated assets.");
                }

                if ($product->consumableStocks()->exists()) {
                    throw ProductException::inUse("Cannot delete product '{$product->name}' because it has associated consumable stocks.");
                }

                $product->delete();
            } catch (ProductException $e) {
                throw $e;
            } catch (Throwable $e) {
                throw ProductException::deletionFailed((string) $product->id, $e->getMessage(), $e);
            }
        });
    }

    /**
     * Get all products, ordered by newest.
     *
     * @return Collection<int, Product>
     */
    public function getAllProducts(): Collection
    {
        return Product::latest()->get();
    }
}
