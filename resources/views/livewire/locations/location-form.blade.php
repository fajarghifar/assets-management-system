<div>
    <x-modal name="location-form-modal" :title="''" maxWidth="2xl">
        <div class="p-6">
            <!-- Custom Header -->
            <div class="mb-6 space-y-1.5 text-center sm:text-left">
                <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                    {{ $isEditing ? 'Edit Location' : 'Add New Location' }}
                </h3>
                <p class="text-sm text-muted-foreground">
                    {{ $isEditing ? 'Make changes to your location here. Click save when you\'re done.' : 'Add a new location to your workspace. Click save when you\'re done.' }}
                </p>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Code -->
                    <x-form-input
                        name="code"
                        label="Location Code"
                        placeholder="e.g. JMP1-RIT"
                        required
                        wire:model="code"
                    />

                    <!-- Site (Searchable) -->
                    <div
                        x-data="{
                            open: false,
                            query: '',
                            selected: @entangle('site'),
                            options: {{ \Illuminate\Support\Js::from($sites) }},
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
                        <x-input-label for="site" value="Site" :required="true" />

                        <div class="relative mt-1">
                            <input
                                type="text"
                                x-model="query"
                                x-on:focus="open = true"
                                x-on:click.away="open = false"
                                x-on:keydown.escape="open = false"
                                placeholder="Select or search a site..."
                                class="block w-full h-10 rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm"
                            />

                            <!-- Chevron -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <x-heroicon-o-chevron-up-down class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </div>

                        <!-- Dropdown -->
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

                        <x-input-error :messages="$errors->get('site')" class="mt-2" />
                    </div>
                </div>

                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Name"
                    placeholder="e.g. IT Room"
                    required
                    wire:model="name"
                />

                <!-- Description -->
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea
                        id="description"
                        wire:model="description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm"
                        placeholder="Optional description..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-x-2 pt-4">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'location-form-modal' })">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        <span wire:loading.remove wire:target="save">
                            {{ __('Save changes') }}
                        </span>
                        <span wire:loading wire:target="save">
                            {{ __('Saving...') }}
                        </span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
