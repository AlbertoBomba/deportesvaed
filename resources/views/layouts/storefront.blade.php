<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>{{ config('app.name') }}</title>
    <meta
        name="description"
        content="Ropa y equipamiento deportivo para moverte a tu manera. Descubre la selección de {{ config('app.name') }}."
    >
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link
        rel="icon"
        href="{{ asset('favicon.ico') }}"
    >
    @livewireStyles
</head>

<body class="antialiased">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-full focus:bg-white focus:px-6 focus:py-3" href="#contenido">Saltar al contenido</a>
    @livewire('components.navigation')

    <main id="contenido" tabindex="-1">
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
</body>

</html>
