<x-modal name="consumable-stock-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Custom Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Edit Stock' : 'Create Stock' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Update stock quantity and thresholds.' : 'Add new stock for a product at a location.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="space-y-4" x-data="{ productId: @entangle('product_id'), locationId: @entangle('location_id') }">
                <!-- Product (Searchable) -->
                <div>
                    <x-searchable-select
                        name="product_id"
                        label="Product"
                        x-model="productId"
                        :url="route('ajax.products')"
                        :options="$productOptions"
                        placeholder="Search Product..."
                        required
                        :disabled="$isEditing"
                    />
                    @if($isEditing)
                        <p class="text-xs text-muted-foreground mt-1">Product cannot be changed while editing.</p>
                    @endif
                </div>

                <!-- Location (Searchable) -->
                <div>
                    <x-searchable-select
                        name="location_id"
                        label="Location"
                        x-model="locationId"
                        :url="route('ajax.locations')"
                        :options="$locationOptions"
                        placeholder="Search Location..."
                        required
                        :disabled="$isEditing"
                    />
                    @if($isEditing)
                        <p class="text-xs text-muted-foreground mt-1">Location cannot be changed while editing.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Quantity -->
                <x-form-input
                    name="quantity"
                    label="Quantity"
                    type="number"
                    wire:model="quantity"
                    placeholder="0"
                    min="0"
                    required
                />

                <!-- Min Quantity -->
                <x-form-input
                    name="min_quantity"
                    label="Minimum Stock Alert"
                    type="number"
                    wire:model="min_quantity"
                    placeholder="0"
                    min="0"
                    required
                />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'consumable-stock-form-modal' })">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button type="submit">
                    <x-heroicon-o-check class="w-4 h-4 mr-2" />
                    {{ $isEditing ? __('Save Changes') : __('Create Stock') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
