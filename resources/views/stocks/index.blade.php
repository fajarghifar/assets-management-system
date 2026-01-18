<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Consumable Stocks') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-stock')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Create Stock') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:stocks.consumable-stocks-table />
        </div>
    </div>

    <livewire:stocks.consumable-stock-form />
    <livewire:stocks.consumable-stock-detail />
</x-app-layout>
