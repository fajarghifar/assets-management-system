<x-modal name="stock-form-modal" :title="''" maxWidth="2xl">
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
            <div class="space-y-4">
                <!-- Product (Searchable) -->
                <div
                    x-data="{
                        open: false,
                        query: '',
                        selected: @entangle('product_id'),
                        options: {{ \Illuminate\Support\Js::from($productOptions) }},
                        init() {
                            this.$watch('selected', (value) => {
                                if (!value) { this.query = ''; return; }
                                const option = this.options.find(o => o.value === value);
                                if (option) this.query = option.label;
                            });
                            if (this.selected) {
                                const option = this.options.find(o => o.value === this.selected);
                                if (option) this.query = option.label;
                            }
                        },
                        get filteredOptions() {
                            if (this.query === '') return this.options;
                            return this.options.filter(option =>
                                option.label.toLowerCase().includes(this.query.toLowerCase())
                            );
                        },
                        selectOption(option) {
                            this.selected = option.value;
                            this.query = option.label;
                            this.open = false;
                        }
                    }"
                    class="relative"
                >
                    <x-input-label for="product_id" value="Product" required />

                    <div class="relative mt-1">
                        <input
                            type="text"
                            x-model="query"
                            x-on:focus="open = true"
                            x-on:click.away="open = false"
                            x-on:keydown.escape="open = false"
                            placeholder="Select product..."
                            class="block w-full h-10 rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed"
                            {{ $isEditing ? 'disabled' : '' }}
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <x-heroicon-o-chevron-up-down class="w-5 h-5 text-muted-foreground" />
                        </div>
                    </div>

                    <div
                        x-show="open && filteredOptions.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-10 w-full mt-1 bg-popover text-popover-foreground rounded-md shadow-lg max-h-60 overflow-auto border border-border"
                        style="display: none;"
                    >
                        <ul class="py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <template x-for="option in filteredOptions" :key="option.value">
                                <li
                                    x-on:click="selectOption(option)"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-accent hover:text-accent-foreground"
                                >
                                    <span x-text="option.label" class="block truncate" :class="{ 'font-semibold': selected === option.value, 'font-normal': selected !== option.value }"></span>
                                    <span x-show="selected === option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 dark:text-indigo-400">
                                        <x-heroicon-o-check class="w-5 h-5" />
                                    </span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    @if($isEditing)
                        <p class="text-xs text-muted-foreground mt-1">Product cannot be changed while editing.</p>
                    @endif
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <!-- Location (Searchable) -->
                <div
                    x-data="{
                        open: false,
                        query: '',
                        selected: @entangle('location_id'),
                        options: {{ \Illuminate\Support\Js::from($locationOptions) }},
                        init() {
                            this.$watch('selected', (value) => {
                                if (!value) { this.query = ''; return; }
                                const option = this.options.find(o => o.value === value);
                                if (option) this.query = option.label;
                            });
                            if (this.selected) {
                                const option = this.options.find(o => o.value === this.selected);
                                if (option) this.query = option.label;
                            }
                        },
                        get filteredOptions() {
                            if (this.query === '') return this.options;
                            return this.options.filter(option =>
                                option.label.toLowerCase().includes(this.query.toLowerCase())
                            );
                        },
                        selectOption(option) {
                            this.selected = option.value;
                            this.query = option.label;
                            this.open = false;
                        }
                    }"
                    class="relative"
                >
                    <x-input-label for="location_id" value="Location" required />

                    <div class="relative mt-1">
                        <input
                            type="text"
                            x-model="query"
                            x-on:focus="open = true"
                            x-on:click.away="open = false"
                            x-on:keydown.escape="open = false"
                            placeholder="Select location..."
                            class="block w-full h-10 rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed"
                            {{ $isEditing ? 'disabled' : '' }}
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <x-heroicon-o-chevron-up-down class="w-5 h-5 text-muted-foreground" />
                        </div>
                    </div>

                    <div
                        x-show="open && filteredOptions.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-10 w-full mt-1 bg-popover text-popover-foreground rounded-md shadow-lg max-h-60 overflow-auto border border-border"
                        style="display: none;"
                    >
                        <ul class="py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <template x-for="option in filteredOptions" :key="option.value">
                                <li
                                    x-on:click="selectOption(option)"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-accent hover:text-accent-foreground"
                                >
                                    <span x-text="option.label" class="block truncate" :class="{ 'font-semibold': selected === option.value, 'font-normal': selected !== option.value }"></span>
                                    <span x-show="selected === option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 dark:text-indigo-400">
                                        <x-heroicon-o-check class="w-5 h-5" />
                                    </span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    @if($isEditing)
                        <p class="text-xs text-muted-foreground mt-1">Location cannot be changed while editing.</p>
                    @endif
                    <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
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
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'stock-form-modal' })">
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
