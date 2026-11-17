<footer class="bg-ink text-paper">
    <div class="store-container">
        <div class="grid gap-10 py-12 sm:py-16 md:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <a href="{{ route('home') }}" wire:navigate aria-label="Volver al inicio"><x-brand.logo /></a>
                <p class="mt-5 max-w-xs text-sm leading-7 text-white/60">El deporte no es solo lo que haces.<br>Es lo que te mueve.</p>
            </div>
            <div><p class="eyebrow text-white/50">Encuentra tu ritmo</p><a class="mt-5 flex items-center gap-3 text-sm hover:text-orange-300" href="{{ route('search.view') }}" wire:navigate>Todos los productos <x-store-icon class="size-4" /></a><a class="mt-4 block text-sm hover:text-orange-300" href="{{ route('home') }}" wire:navigate>Inicio</a></div>
            <div><p class="eyebrow text-white/50">Sin complicaciones</p><p class="mt-5 text-sm leading-7 text-white/70">Elige tu equipación.<br>Encuentra tu talla.<br>Sal a darlo todo.</p></div>
        </div>
        <p class="overflow-hidden border-b border-white/15 pb-6 text-[clamp(3rem,12vw,11rem)] font-black uppercase leading-none tracking-[-0.06em] text-white/10" aria-hidden="true">Keep moving.</p>
        <div class="flex flex-wrap justify-between gap-3 py-6 text-[11px] text-white/50"><p>&copy; {{ now()->year }} {{ config('app.name') }}.</p><p>Tu próxima meta empieza aquí.</p></div>
    </div>
</footer>
