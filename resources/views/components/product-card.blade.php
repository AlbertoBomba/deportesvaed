@props(['product'])

<a class="group block min-w-0" href="{{ route('product.view', $product->defaultUrl->slug) }}" wire:navigate>
    <div class="relative aspect-[4/5] overflow-hidden rounded-xl border border-ink/5 bg-[#eeeee8]">
        @if ($product->thumbnail)
            <img class="h-full w-full object-contain p-4 mix-blend-multiply motion-safe:transition-transform motion-safe:duration-500 motion-safe:group-hover:scale-105"
                src="{{ $product->thumbnail->getAvailableUrl(['medium']) }}" alt="{{ $product->translateAttribute('name') }}" loading="lazy" width="480" height="600" />
        @else
            <div class="flex h-full flex-col items-center justify-center gap-3 text-muted"><x-store-icon name="bag" class="size-12 opacity-40" /><span class="text-xs">Imagen no disponible</span></div>
        @endif
        <span class="absolute right-3 bottom-3 flex size-9 items-center justify-center rounded-full bg-white text-ink transition-colors group-hover:bg-accent group-hover:text-white" aria-hidden="true"><x-store-icon class="size-4" /></span>
    </div>
    <div class="flex flex-col gap-2 pt-4 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
        <h3 class="text-sm leading-5 font-semibold group-hover:text-accent">{{ $product->translateAttribute('name') }}</h3>
        <p class="shrink-0 text-sm font-bold"><span class="sr-only">Precio: </span><x-product-price :product="$product" /></p>
    </div>
</a>
