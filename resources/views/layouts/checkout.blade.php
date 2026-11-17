<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Finalizar compra - {{ config('app.name') }}</title>
    <meta
        name="description"
        content="Revisa tu equipación y completa tu pedido en {{ config('app.name') }}."
    >
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
        rel="icon"
        href="{{ asset('favicon.ico') }}"
    >
    @livewireStyles
</head>

<body class="antialiased">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:bg-white focus:p-4" href="#contenido">Saltar al contenido</a>
    <header class="border-b border-ink/15">
        <div class="store-container flex min-h-24 flex-wrap items-center justify-between gap-5 py-5">
            <a
                class="flex items-center shrink-0"
                href="{{ route('home') }}"
                wire:navigate
            >
                <span class="sr-only">Inicio</span>

                <x-brand.logo />
            </a>
            <a class="flex items-center gap-2 text-xs font-semibold text-muted hover:text-accent" href="{{ route('search.view') }}" wire:navigate>Seguir comprando <x-store-icon class="size-4" /></a>
        </div>
    </header>


    <main id="contenido" tabindex="-1">
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
</body>

</html>
