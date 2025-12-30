<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function saving(Category $category): void
    {
        if (blank($category->slug) || ($category->isDirty('name') && ! $category->isDirty('slug'))) {
            $category->slug = Str::slug($category->name);
        }

        if ($category->isDirty('slug')) {
            $category->slug = Str::slug($category->slug);
        }
    }
}
