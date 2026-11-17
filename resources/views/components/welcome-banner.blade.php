<section class="store-container py-5 sm:py-8">
    <div class="relative isolate overflow-hidden rounded-2xl bg-[#e6e8df] lg:rounded-3xl">
        <div class="grid lg:min-h-[590px] lg:grid-cols-2">
            <div class="relative z-10 flex flex-col items-start px-7 py-12 sm:px-12 sm:py-16 lg:px-14 lg:py-20">
                <p class="eyebrow flex items-center gap-2.5"><span class="size-2 rounded-full bg-accent"></span> Hecho para seguirte el ritmo</p>
                <h1 class="display-title mt-9 text-[clamp(3rem,7.5vw,7.5rem)]">No pares.<br>Que nada<br><span class="text-accent">te pare.</span></h1>
                <p class="mt-7 max-w-xs text-sm leading-7 text-muted">Para los primeros kilómetros. Para una repetición más. Para todo lo que te mueve.</p>
                <a class="button-primary mt-8" href="{{ route('search.view') }}" wire:navigate>Encuentra tu equipación <x-store-icon /></a>
            </div>
            <div class="relative min-h-72 overflow-hidden lg:min-h-full">
                <img class="absolute inset-0 h-full w-full object-cover object-[58%_center]" src="{{ asset('images/running-editorial.jpg') }}"
                    alt="Corredores entrenando juntos al aire libre" width="1800" height="1200" fetchpriority="high" />
                <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute right-7 bottom-7 left-7 flex items-end justify-between text-white sm:right-10 sm:bottom-10 sm:left-10">
                    <p class="text-3xl font-black uppercase leading-none tracking-tighter">Menos excusas.<br>Más movimiento.</p>
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full border border-white/60" aria-hidden="true">↗</span>
                </div>
            </div>
        </div>
        <span class="pointer-events-none absolute top-7 right-7 z-10 rounded-full bg-paper/95 px-4 py-2 text-[10px] font-bold uppercase tracking-widest">Move your way</span>
    </div>
</section>
