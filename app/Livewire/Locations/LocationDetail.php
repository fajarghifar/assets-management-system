<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use App\Models\Location;
use Livewire\Attributes\On;

class LocationDetail extends Component
{
    public ?Location $location = null;

    public function render()
    {
        return view('livewire.locations.location-detail');
    }

    #[On('show-location')]
    public function show(Location $location)
    {
        $this->location = $location;
        $this->dispatch('open-modal', name: 'location-detail-modal');
    }

    public function closeModal()
    {
        $this->location = null;
    }
}
