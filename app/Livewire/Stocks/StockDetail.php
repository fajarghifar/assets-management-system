<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\ConsumableStock;
use Livewire\Attributes\On;

class StockDetail extends Component
{
    public ?ConsumableStock $stock = null;

    public function render()
    {
        return view('livewire.stocks.stock-detail');
    }

    #[On('show-stock')]
    public function show(ConsumableStock $stock)
    {
        $this->stock = $stock->load(['product', 'location']);
        $this->dispatch('open-modal', name: 'stock-detail-modal');
    }

    public function closeModal()
    {
        $this->stock = null;
    }
}
