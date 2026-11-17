<div>
    <x-welcome-banner />

    <div class="store-container">
        <div class="grid gap-5 border-b border-ink/15 py-6 sm:grid-cols-3 sm:gap-8">
            @foreach (['01' => ['A tu manera', 'Tu deporte, sin etiquetas.'], '02' => ['Encuentra tu equipo', 'Ropa y accesorios en un mismo lugar.'], '03' => ['Sigue en movimiento', 'Equípate para tu próximo objetivo.']] as $number => [$title, $description])
                <div class="flex items-start gap-4">
                    <span class="eyebrow pt-1 text-accent">{{ $number }}</span>
                    <div><p class="text-sm font-bold">{{ $title }}</p><p class="mt-1 text-xs leading-5 text-muted">{{ $description }}</p></div>
                </div>
            @endforeach
        </div>

        <section class="py-14 sm:py-20" aria-labelledby="latest-products">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <div><p class="eyebrow text-muted">Tu siguiente favorito</p><h2 id="latest-products" class="display-title mt-3 text-4xl sm:text-5xl">Listos para todo.</h2></div>
                <a class="flex items-center gap-3 border-b border-ink pb-2 text-xs font-bold" href="{{ route('search.view') }}" wire:navigate>Explorar la colección <x-store-icon class="size-4" /></a>
            </div>
            <div class="mt-9 grid grid-cols-2 gap-x-4 gap-y-9 md:grid-cols-3 lg:grid-cols-4 lg:gap-x-6">
                @forelse ($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="empty-state"><x-store-icon name="bag" class="size-9" /><p>Estamos preparando nuestra próxima selección.</p><p class="text-sm">Vuelve pronto para descubrir tus nuevos favoritos.</p></div>
                @endforelse
            </div>
        </section>

        <section class="mb-16 grid overflow-hidden rounded-2xl bg-[#e8e8de] md:grid-cols-[1fr_auto]">
            <div class="p-8 sm:p-12"><p class="eyebrow text-muted">El deporte empieza contigo</p><h2 class="display-title mt-5 text-4xl sm:text-6xl">No hace falta ser pro.<br>Solo <span class="text-accent">tener ganas.</span></h2><p class="mt-5 max-w-lg text-sm leading-7 text-muted">Cada persona tiene su ritmo. Aquí encontrarás equipación para disfrutar del camino, sea cual sea tu meta.</p></div>
            <div class="flex items-end p-8 pt-0 sm:p-12 md:pl-0"><a class="button-secondary" href="{{ route('search.view') }}" wire:navigate>Vamos a por ello <x-store-icon /></a></div>
        </section>
    </div>
</div>
