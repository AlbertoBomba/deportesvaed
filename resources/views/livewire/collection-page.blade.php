<section class="store-container py-8 sm:py-12">
    <nav class="flex flex-wrap gap-2 text-xs text-muted" aria-label="Ruta de navegación"><a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Inicio</a><span aria-hidden="true">/</span><span aria-current="page">{{ $this->collection->translateAttribute('name') }}</span></nav>
    <div class="mt-8 flex flex-wrap items-end justify-between gap-6 border-b border-ink/15 pb-8">
        <div><p class="eyebrow text-accent">Encuentra tu próxima equipación</p><h1 class="display-title mt-4 text-4xl sm:text-6xl">{{ $this->collection->translateAttribute('name') }}</h1></div>
        <p class="text-sm text-muted">{{ $products->total() }} {{ $products->total() === 1 ? 'producto' : 'productos' }}</p>
    </div>
    <div class="grid grid-cols-2 gap-x-4 gap-y-9 py-9 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="empty-state"><x-store-icon name="bag" class="size-10" /><h2 class="text-lg font-bold text-ink">Esta colección está calentando motores.</h2><p class="text-sm">Todavía no hay productos disponibles.</p><a class="button-secondary" href="{{ route('search.view') }}" wire:navigate>Explorar otros productos <x-store-icon /></a></div>
        @endforelse
    </div>
    <div class="pb-8">{{ $products->links() }}</div>
</section>
