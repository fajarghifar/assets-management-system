<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\DTOs\AssetData;
use Livewire\Component;
use App\Models\Location;
use Livewire\Attributes\On;
use App\Services\AssetService;
use App\Exceptions\AssetException;

class MoveAsset extends Component
{
    public ?Asset $asset = null;
    public $location_id; // New Location
    public $recipient_name;
    public $notes;

    public $locationOptions = [];

    protected function rules()
    {
        return [
            'location_id' => 'required|exists:locations,id|different:asset.location_id',
            'recipient_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->locationOptions = Location::orderBy('name')->get()->map(function ($location) {
            return [
                'value' => $location->id,
                'label' => $location->full_name,
            ];
        })->toArray();
    }

    #[On('move-asset')]
    public function openModal($assetId)
    {
        $this->asset = Asset::find($assetId);

        if ($this->asset) {
            $this->reset(['location_id', 'recipient_name', 'notes']);
            $this->dispatch('open-modal', name: 'move-asset-modal');
        }
    }

    public function save(AssetService $assetService)
    {
        $this->validate();

        try {
            // Create minimal DTO for movement
            $data = AssetData::fromArray([
                'product_id' => $this->asset->product_id, // Preserved
                'location_id' => $this->location_id,
                'status' => $this->asset->status, // Status preserved
                'recipient_name' => $this->recipient_name,
                'history_notes' => $this->notes,
            ]);

            $assetService->updateAsset($this->asset, $data);

            $this->dispatch('close-modal', name: 'move-asset-modal');
            $this->dispatch('pg:eventRefresh-assets-table');
            $this->dispatch('toast', message: 'Asset moved successfully.', type: 'success');

        } catch (AssetException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Failed to move asset.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.assets.move-asset');
    }
}
