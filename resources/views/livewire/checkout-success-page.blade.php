<section class="store-container py-16 sm:py-24">
    @if ($order)
        <div class="mx-auto max-w-2xl rounded-3xl border border-ink/10 bg-white px-6 py-12 text-center sm:px-12">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-[#e6e8df] text-ink"><x-store-icon name="check" class="size-8" /></span>
            <p class="eyebrow mt-7 text-accent">Gracias por elegirnos</p>
            <h1 class="display-title mt-4 text-4xl sm:text-6xl">¡Buen fichaje!</h1>
            <p class="mt-6 text-sm leading-7 text-muted">Hemos recibido tu pedido. Guarda esta referencia para identificarlo:</p>
            <p class="mt-5 rounded-xl bg-paper px-5 py-4 text-2xl font-bold tracking-wider">{{ $order->reference }}</p>
            <div class="mt-6 rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm leading-6 text-orange-900"><strong class="block">Pago pendiente</strong>El pago se realiza fuera de la web. No se ha realizado ningún cargo online.</div>
            <a class="button-primary mt-8" href="{{ route('search.view') }}" wire:navigate>Seguir explorando <x-store-icon /></a>
        </div>
    @endif
</section>
