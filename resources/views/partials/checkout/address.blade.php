<form wire:submit="saveAddress('{{ $type }}')" class="checkout-panel">
    <div class="flex min-h-16 flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-6 py-3">
        <h2 class="flex items-center gap-3 text-base font-bold"><span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-paper text-xs text-accent">{{ $step }}</span>{{ $type === 'shipping' ? 'Dirección de envío' : 'Datos de facturación' }}</h2>
        @if ($type === 'shipping' && $step === $currentStep)
            <label class="flex items-center gap-2 text-xs">
                <input type="checkbox" wire:model="shippingIsBilling">
                Usar para facturación
            </label>
        @endif
        @if ($currentStep > $step)
            <button type="button" class="text-xs font-bold text-accent"
                    wire:click="$set('currentStep', {{ $step }})">Editar</button>
        @endif
    </div>
    @if ($currentStep >= $step)
        <div class="p-6">
            @if ($currentStep === $step)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        'first_name' => 'Nombre', 'last_name' => 'Apellidos',
                        'company_name' => 'Empresa (opcional)', 'contact_phone' => 'Teléfono (opcional)',
                        'contact_email' => 'Correo electrónico', 'line_one' => 'Dirección',
                        'line_two' => 'Piso, puerta, etc. (opcional)', 'line_three' => 'Información adicional (opcional)',
                        'city' => 'Ciudad', 'state' => 'Provincia (opcional)', 'postcode' => 'Código postal',
                    ] as $field => $label)
                        <x-input.group :label="$label" :errors="$errors->get($type.'.'.$field)"
                                       :required="in_array($field, ['first_name', 'last_name', 'contact_email', 'line_one', 'city', 'postcode'])">
                            <x-input.text wire:model="{{ $type }}.{{ $field }}"
                                          :error="$errors->has($type.'.'.$field)"
                                          :type="$field === 'contact_email' ? 'email' : 'text'" />
                        </x-input.group>
                    @endforeach
                    <x-input.group label="País" :errors="$errors->get($type.'.country_id')" required>
                        <select class="min-h-12 w-full rounded-lg border border-ink/20 bg-paper/50 p-3 text-sm"
                                wire:model="{{ $type }}.country_id">
                            <option value="">Selecciona un país</option>
                            @foreach ($this->countries as $country)
                                <option value="{{ $country->id }}">{{ $country->native ?: $country->name }}</option>
                            @endforeach
                        </select>
                    </x-input.group>
                </div>
                <button class="button-primary mt-6"
                        type="submit" wire:loading.attr="disabled" wire:target="saveAddress">
                    Guardar y continuar <x-store-icon class="size-4" />
                </button>
            @else
                <div class="text-sm text-gray-700 space-y-1">
                    @foreach (['first_name', 'last_name', 'company_name', 'line_one', 'line_two', 'line_three', 'city', 'state', 'postcode', 'contact_email', 'contact_phone'] as $field)
                        @if (! empty($this->{$type}[$field]))
                            <p>{{ $this->{$type}[$field] }}</p>
                        @endif
                    @endforeach
                    <p>{{ $this->countries->firstWhere('id', $this->{$type}['country_id'] ?? null)?->name }}</p>
                </div>
            @endif
        </div>
    @endif
</form>
