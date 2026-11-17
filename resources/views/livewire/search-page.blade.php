<section class="store-container py-8 sm:py-12">
    <nav class="flex gap-2 text-xs text-muted" aria-label="Ruta de navegación"><a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Inicio</a><span aria-hidden="true">/</span><span aria-current="page">La colección</span></nav>
    <div class="mt-8 grid items-end gap-7 border-b border-ink/15 pb-8 md:grid-cols-2">
        <div>
            <p class="eyebrow text-accent">{{ $term !== '' ? 'Encuentra lo que te mueve' : 'Equipación para tu día a día' }}</p>
            <h1 class="display-title mt-4 text-4xl sm:text-6xl">{{ $term !== '' ? 'Tu búsqueda.' : 'La colección.' }}</h1>
            @if ($term !== '')<p class="mt-4 text-sm text-muted">Resultados para <strong class="text-ink">«{{ $term }}»</strong></p>@endif
        </div>
        <form action="{{ route('search.view') }}" class="relative md:ml-auto md:w-full md:max-w-md" role="search">
            <label for="catalog-search" class="sr-only">Buscar en el catálogo</label>
            <input id="catalog-search" type="search" name="term" value="{{ $term }}" maxlength="200" placeholder="Busca por producto o referencia" class="min-h-14 w-full rounded-full border border-ink/20 bg-white pr-16 pl-6 text-sm" />
            <button type="submit" class="absolute top-1.5 right-1.5 flex size-11 items-center justify-center rounded-full bg-ink text-white hover:bg-accent" aria-label="Buscar productos"><x-store-icon name="search" /></button>
        </form>
    </div>
    <p class="mt-6 text-xs text-muted" role="status">{{ $results->total() }} {{ $results->total() === 1 ? 'producto' : 'productos' }}</p>
    <div class="grid grid-cols-2 gap-x-4 gap-y-9 pt-6 pb-10 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
        @forelse ($results as $result)
            <x-product-card :product="$result" />
        @empty
            <div class="empty-state"><x-store-icon name="search" class="size-10" /><h2 class="text-xl font-bold text-ink">No hemos encontrado productos.</h2><p class="max-w-sm text-sm leading-6">Prueba con otro nombre o referencia. Tu próxima equipación puede estar a una búsqueda de distancia.</p>@if ($term !== '')<a class="button-secondary" href="{{ route('search.view') }}" wire:navigate>Ver toda la colección <x-store-icon /></a>@endif</div>
        @endforelse
    </div>
    <div class="pb-8">{{ $results->links() }}</div>
</section>
