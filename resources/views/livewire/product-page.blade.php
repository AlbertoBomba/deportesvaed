<section class="store-container py-8 sm:py-10">
    <nav class="mb-8 flex flex-wrap gap-2 text-xs text-muted" aria-label="Ruta de navegación">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Inicio</a><span aria-hidden="true">/</span>
        <a href="{{ route('search.view') }}" wire:navigate class="hover:text-accent">La colección</a><span aria-hidden="true">/</span>
        <span aria-current="page">{{ $this->product->translateAttribute('name') }}</span>
    </nav>
    <div class="grid items-start gap-9 pb-14 lg:grid-cols-[1.15fr_1fr] lg:gap-16">
        <div x-data="{ activeImage: @js($this->image?->getAvailableUrl(['large', 'medium'])) }" wire:key="gallery_{{ $this->variant?->id ?? 'unavailable' }}">
            <div class="flex aspect-[4/5] items-center justify-center overflow-hidden rounded-2xl bg-[#eeeee8]">
                @if ($this->image)
                    <img class="h-full w-full object-contain p-6 mix-blend-multiply sm:p-10" src="{{ $this->image->getAvailableUrl(['large', 'medium']) }}" x-bind:src="activeImage"
                        alt="{{ $this->product->translateAttribute('name') }}" width="800" height="1000" fetchpriority="high" />
                @else
                    <div class="flex flex-col items-center gap-4 text-muted"><x-store-icon name="bag" class="size-16 opacity-40" /><p class="text-sm">Imagen no disponible</p></div>
                @endif
            </div>
            @if ($this->image && $this->images->isNotEmpty())
                <div class="mt-4 flex gap-3 overflow-x-auto p-1" aria-label="Imágenes del producto">
                    @foreach ($this->images as $image)
                        <button class="size-20 shrink-0 overflow-hidden rounded-lg border-2 bg-[#eeeee8] sm:size-24" type="button" wire:key="image_{{ $image->id }}"
                            x-on:click="activeImage = @js($image->getAvailableUrl(['large', 'medium']))"
                            x-bind:class="activeImage === @js($image->getAvailableUrl(['large', 'medium'])) ? 'border-accent' : 'border-transparent'"
                            x-bind:aria-pressed="activeImage === @js($image->getAvailableUrl(['large', 'medium']))" aria-label="Ver imagen {{ $loop->iteration }}">
                            <img class="h-full w-full object-contain p-2 mix-blend-multiply" src="{{ $image->getAvailableUrl(['small']) }}" alt="" loading="lazy" width="100" height="100" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="lg:sticky lg:top-8">
            <p class="eyebrow text-accent">Tu próxima equipación</p>
            <h1 class="mt-4 text-3xl leading-tight font-extrabold tracking-[-0.04em] sm:text-5xl">{{ $this->product->translateAttribute('name') }}</h1>
            <p class="mt-3 text-xs text-muted">Referencia: {{ $this->variant?->sku ?? '—' }}</p>
            @if ($this->variant)<p class="mt-6 text-2xl font-bold"><x-product-price :variant="$this->variant" /></p>@endif
            <p class="mt-3 text-xs leading-5 text-muted">Envío e impuestos desglosados al finalizar la compra.</p>
            <div class="my-7 border-t border-ink/15"></div>
            <div class="space-y-6">
                @foreach ($this->productOptions as $option)
                    <fieldset>
                        <legend class="text-sm font-bold">{{ $option['option']->translate('name') }}</legend>
                        <div class="mt-3 flex flex-wrap gap-2" x-data="{ selectedOption: @entangle('selectedOptionValues').live }">
                            @foreach ($option['values'] as $value)
                                <button class="min-h-12 min-w-14 rounded-lg border px-5 py-3 text-sm font-semibold transition-colors" type="button"
                                    wire:click="$set('selectedOptionValues.{{ $option['option']->id }}', {{ $value->id }})"
                                    x-bind:aria-pressed="Object.values(selectedOption).map(Number).includes({{ $value->id }})"
                                    x-bind:class="Object.values(selectedOption).map(Number).includes({{ $value->id }}) ? 'border-ink bg-ink text-white' : 'border-ink/20 hover:border-ink'">
                                    {{ $value->translate('name') }}
                                </button>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>
            <div class="mt-8">
                @if ($this->variant)
                    <livewire:components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
                @else
                    <p class="rounded-xl bg-orange-50 p-4 text-sm text-orange-900" role="status">Esta combinación no está disponible.</p>
                @endif
            </div>
            <div class="mt-6 flex items-start gap-3 text-xs leading-6 text-muted"><x-store-icon name="shield" class="mt-0.5 size-5 shrink-0" /><p>Revisa tu selección y todos los detalles del pedido antes de confirmarlo.</p></div>
            <details class="mt-8 border-y border-ink/15 py-5" open>
                <summary class="text-sm font-bold">Detalles del producto</summary>
                <div class="mt-4 text-sm leading-7 text-muted">{{ strip_tags($this->product->translateAttribute('description') ?? '') ?: 'Prepárate para tu próximo objetivo. Selecciona las opciones disponibles y añade el producto a tu bolsa.' }}</div>
            </details>
            <a class="mt-6 inline-flex items-center gap-3 text-xs font-semibold hover:text-accent" href="{{ route('search.view') }}" wire:navigate>Seguir explorando <x-store-icon class="size-4" /></a>
        </div>
    </div>
</section>
