<header class="relative z-40 border-b border-ink/10 bg-paper" x-data="{ mobileMenu: false }" x-on:keydown.escape.window="mobileMenu = false">
    <div class="bg-ink px-5 py-2.5 text-center text-[10px] font-semibold uppercase tracking-[0.18em] text-white sm:text-[11px]">
        Tu ritmo. Tu deporte. Tu próxima versión.
    </div>
    <div class="store-container flex min-h-22 flex-wrap items-center justify-between gap-5 py-5">
        <a class="shrink-0" href="{{ route('home') }}" wire:navigate aria-label="{{ config('app.name') }} — Inicio">
            <x-brand.logo />
        </a>
        <nav class="hidden items-center gap-7 xl:flex" aria-label="Navegación principal">
            <a href="{{ route('search.view') }}" wire:navigate class="text-sm font-bold hover:text-accent">La colección</a>
            @foreach ($this->collections->take(4) as $collection)
                <a class="text-sm text-muted transition-colors hover:text-accent" href="{{ route('collection.view', $collection->defaultUrl->slug) }}" wire:navigate>
                    {{ $collection->translateAttribute('name') }}
                </a>
            @endforeach
        </nav>
        <div class="flex items-center gap-3">
            <x-header.search class="hidden max-w-64 md:block" />
            @livewire('components.cart')
            <button type="button" class="flex size-11 items-center justify-center rounded-full border border-ink/15 xl:hidden"
                x-on:click="mobileMenu = !mobileMenu" x-bind:aria-expanded="mobileMenu" aria-controls="mobile-menu" aria-label="Abrir navegación">
                <x-store-icon name="menu" />
            </button>
        </div>
        <div id="mobile-menu" x-cloak x-show="mobileMenu" x-transition class="w-full border-t border-ink/10 pt-5 xl:hidden">
            <div class="md:hidden"><x-header.search id="mobile-search" /></div>
            <nav class="mt-4 flex flex-col divide-y divide-ink/10" aria-label="Navegación móvil">
                <a class="py-3 font-bold" href="{{ route('search.view') }}" wire:navigate>Ver todos los productos <span aria-hidden="true">↗</span></a>
                @foreach ($this->collections as $collection)
                    <a class="py-3 text-sm" href="{{ route('collection.view', $collection->defaultUrl->slug) }}" wire:navigate>{{ $collection->translateAttribute('name') }}</a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
