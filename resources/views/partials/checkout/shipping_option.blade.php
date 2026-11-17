<form wire:submit="saveShippingOption"
      class="checkout-panel">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100">
        <h2 class="flex items-center gap-3 text-base font-bold">
            <span class="flex size-8 items-center justify-center rounded-full bg-paper text-xs text-accent">{{ $step }}</span> Método de envío
        </h2>

        @if ($currentStep > $step)
            <button class="px-5 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-700"
                    type="button"
                    wire:click.prevent="$set('currentStep', {{ $step }})">
                Editar
            </button>
        @endif
    </div>

    @if ($currentStep >= $step)
        <div class="p-6">
            @if ($currentStep == $step)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($this->shippingOptions as $option)
                        <div>
                            <input class="peer sr-only"
                                   type="radio"
                                   wire:model.live="chosenShipping"
                                   name="shippingOption"
                                   value="{{ $option->getIdentifier() }}"
                                   id="{{ $option->getIdentifier() }}" />

                            <label class="flex min-h-16 cursor-pointer items-center justify-between gap-3 rounded-xl border border-ink/15 p-4 text-sm font-medium hover:bg-paper peer-checked:border-accent peer-checked:bg-paper peer-checked:ring-1 peer-checked:ring-accent peer-focus-visible:outline-2 peer-focus-visible:outline-offset-4 peer-focus-visible:outline-accent"
                                   for="{{ $option->getIdentifier() }}">
                                <p>
                                    {{ $option->getName() }}
                                </p>

                                <p>
                                    {{ $option->getPrice()->formatted() }}
                                </p>
                            </label>
                        </div>
                    @empty
                        <p class="col-span-full text-sm text-amber-800" role="alert">
                            No hay métodos de envío disponibles para esta dirección. Contacta con la tienda.
                        </p>
                    @endforelse
                </div>

                @if ($errors->has('chosenShipping'))
                    <p class="p-4 text-sm text-red-500">
                        {{ $errors->first('chosenShipping') }}
                    </p>
                @endif
            @elseif($currentStep > $step && $this->shippingOption)
                <dl class="flex flex-wrap max-w-xs text-sm">
                    <dt class="w-1/2 font-medium">
                        {{ $this->shippingOption->getDescription() }}
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $this->shippingOption->getPrice()->formatted() }}
                    </dd>
                </dl>
            @endif

            @if ($step == $currentStep)
                <div class="mt-6 text-right">
                    <button class="button-primary"
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveShippingOption"
                            @disabled($this->shippingOptions->isEmpty())
                            wire:key="shipping_submit_btn">
                        <span wire:loading.remove.delay
                              wire:target="saveShippingOption">
                            Confirmar envío
                        </span>
                        <span wire:loading.delay
                              wire:target="saveShippingOption">
                            <svg class="w-5 h-5 text-white animate-spin"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75"
                                      fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @endif
</form>
