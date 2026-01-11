<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteModal extends Component
{
    public bool $open = false;
    public string $title = 'Are you absolutely sure?';
    public string $description = 'This action cannot be undone. This will permanently delete your data.';
    public string $component = '';
    public string $method = '';
    public array $params = [];

    #[On('open-delete-modal')]
    public function open(string $component, string $method, array $params = [], string $title = '', string $description = '')
    {
        $this->component = $component;
        $this->method = $method;
        $this->params = $params;

        if ($title) $this->title = $title;
        if ($description) $this->description = $description;

        $this->open = true;
    }

    public function confirm()
    {
        if ($this->component && $this->method) {
            $this->dispatch($this->method, ...$this->params)->to($this->component);
        }
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.components.delete-modal');
    }
}
