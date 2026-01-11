<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use App\Services\CategoryService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class CategoryForm extends Component
{
    public bool $isEditing = false;
    public ?Category $category = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string')]
    public string $description = '';

    public function render()
    {
        return view('livewire.categories.category-form');
    }

    #[On('create-category')]
    public function create(): void
    {
        $this->reset(['name', 'description', 'category', 'isEditing']);
        $this->dispatch('open-modal', name: 'category-form-modal');
    }

    #[On('edit-category')]
    public function edit(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'category-form-modal');
    }

    public function save(CategoryService $service): void
    {
        $this->validate();

        try {
            if ($this->isEditing && $this->category) {
                $service->updateCategory($this->category, [
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
                $message = 'Category updated successfully.';
            } else {
                $service->createCategory([
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
                $message = 'Category created successfully.';
            }

            $this->dispatch('close-modal', name: 'category-form-modal');
            $this->dispatch('pg:eventRefresh-categories-table');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (\Exception $e) {
            $this->addError('name', 'Error saving category: ' . $e->getMessage());
        }
    }
}
