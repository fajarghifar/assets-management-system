<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class CategoryService
{
    /**
     * Create a new category.
     *
     * @param array<string, mixed> $data
     * @return Category
     * @throws Exception
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            try {
                if (!isset($data['slug']) && isset($data['name'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                $category = Category::create($data);

                Log::info("Category created successfully:ID {$category->id}");

                return $category;
            } catch (Exception $e) {
                Log::error("Failed to create category: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array<string, mixed> $data
     * @return Category
     * @throws Exception
     */
    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            try {
                // Auto-update slug if name changes and slug is NOT explicitly provided
                if (isset($data['name']) && !isset($data['slug'])) {
                    if ($data['name'] !== $category->name) {
                        $data['slug'] = Str::slug($data['name']);
                    }
                }

                $category->update($data);
                $category->refresh();

                Log::info("Category updated successfully: ID {$category->id}");

                return $category;
            } catch (Exception $e) {
                Log::error("Failed to update category ID {$category->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return bool
     * @throws Exception
     */
    public function deleteCategory(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            try {
                if ($category->products()->exists()) {
                    throw new Exception("Cannot delete category '{$category->name}' because it has associated products.");
                }

                $category->delete();

                Log::info("Category deleted successfully: ID {$category->id}");

                return true;
            } catch (Exception $e) {
                Log::error("Failed to delete category ID {$category->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Get all categories, ordered by newest.
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::latest()->get();
    }
}
