<form wire:submit="saveAddress('{{ $type }}')" class="bg-white border border-gray-100 rounded-xl">
    <div class="flex items-center justify-between min-h-16 gap-4 px-6 py-3 border-b border-gray-100">
        <h3 class="text-lg font-medium">{{ ucfirst($type) }} details</h3>
        @if ($type === 'shipping' && $step === $currentStep)
            <label class="flex items-center gap-2 text-xs">
                <input type="checkbox" wire:model="shippingIsBilling">
                Same as billing
            </label>
        @endif
        @if ($currentStep > $step)
            <button type="button" class="text-sm text-blue-600"
                    wire:click="$set('currentStep', {{ $step }})">Edit</button>
        @endif
    </div>
    @if ($currentStep >= $step)
        <div class="p-6">
            @if ($currentStep === $step)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        'first_name' => 'First name', 'last_name' => 'Last name',
                        'company_name' => 'Company', 'contact_phone' => 'Contact phone',
                        'contact_email' => 'Contact email', 'line_one' => 'Address line 1',
                        'line_two' => 'Address line 2', 'line_three' => 'Address line 3',
                        'city' => 'City', 'state' => 'State / Province', 'postcode' => 'Postcode',
                    ] as $field => $label)
                        <x-input.group :label="$label" :errors="$errors->get($type.'.'.$field)"
                                       :required="in_array($field, ['first_name', 'last_name', 'contact_email', 'line_one', 'city', 'postcode'])">
                            <x-input.text wire:model="{{ $type }}.{{ $field }}"
                                          :type="$field === 'contact_email' ? 'email' : 'text'" />
                        </x-input.group>
                    @endforeach
                    <x-input.group label="Country" :errors="$errors->get($type.'.country_id')" required>
                        <select class="w-full p-3 border border-gray-200 rounded-lg"
                                wire:model="{{ $type }}.country_id">
                            <option value="">Select a country</option>
                            @foreach ($this->countries as $country)
                                <option value="{{ $country->id }}">{{ $country->native ?: $country->name }}</option>
                            @endforeach
                        </select>
                    </x-input.group>
                </div>
                <button class="px-5 py-3 mt-6 text-sm font-medium text-white bg-blue-600 rounded-lg disabled:opacity-50"
                        type="submit" wire:loading.attr="disabled" wire:target="saveAddress">
                    Save address
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
