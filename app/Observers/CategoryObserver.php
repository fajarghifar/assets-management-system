<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function saving(Category $category): void
    {
        if (blank($category->slug) || $category->isDirty('name')) {
            $category->slug = Str::slug($category->slug ?? $category->name);
        }
    }

    public function deleting(Category $category): void
    {
        if ($category->products()->count() > 0) {
            throw new \Exception("Kategori ini memiliki produk aktif.");
        }
    }
}
