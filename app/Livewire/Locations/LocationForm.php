<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use App\Models\Location;
use App\Enums\LocationSite;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use App\Services\LocationService;
use Illuminate\Validation\Rule as ValidationRule;

class LocationForm extends Component
{
    public ?Location $location = null;

    #[Rule('required|string|max:255|unique:locations,code')]
    public string $code = '';

    #[Rule('required')]
    public string $site = '';

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('nullable|string')]
    public string $description = '';

    public bool $isEditing = false;

    // For Select Input
    public array $sites = [];

    public function mount()
    {
        // Prepare Enum options for select
        foreach (LocationSite::cases() as $site) {
            $this->sites[] = [
                'value' => $site->value,
                'label' => $site->getLabel(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.locations.location-form');
    }

    #[On('create-location')]
    public function create()
    {
        $this->reset(['code', 'site', 'name', 'description', 'location', 'isEditing']);
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'location-form-modal');
    }

    #[On('edit-location')]
    public function edit(Location $location)
    {
        $this->resetValidation();
        $this->location = $location;
        $this->code = $location->code;
        $this->site = $location->site->value;
        $this->name = $location->name;
        $this->description = $location->description ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'location-form-modal');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', name: 'location-form-modal');
        $this->reset(['code', 'site', 'name', 'description', 'location', 'isEditing']);
        $this->resetValidation();
    }

    public function save(LocationService $service)
    {
        $rules = [
            'code' => [
                'required',
                'string',
                'max:255',
                ValidationRule::unique('locations', 'code')->ignore($this->location?->id)
            ],
            'site' => ['required', ValidationRule::enum(LocationSite::class)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->isEditing && $this->location) {
                $service->updateLocation($this->location, $validated);
                $message = 'Location updated successfully.';
            } else {
                $service->createLocation($validated);
                $message = 'Location created successfully.';
            }

            $this->dispatch('close-modal', name: 'location-form-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('toast', message: $message, type: 'success');

            $this->reset(['code', 'site', 'name', 'description', 'location', 'isEditing']);

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
