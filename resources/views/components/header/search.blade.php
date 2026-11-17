@props(['id' => 'header-search'])

<form {{ $attributes->merge(['class' => 'relative w-full']) }} action="{{ route('search.view') }}" role="search">
    <label class="sr-only" for="{{ $id }}">Buscar productos</label>
    <input id="{{ $id }}" name="term" type="search" maxlength="200" placeholder="¿Qué necesitas para moverte?"
        class="h-11 w-full rounded-full border border-ink/10 bg-paper py-2 pr-4 pl-11 text-sm placeholder:text-muted"
        value="{{ $this->term }}" />
    <button class="absolute top-0 left-0 flex size-11 items-center justify-center text-muted" aria-label="Buscar">
        <x-store-icon name="search" class="size-4" />
    </button>
</form>
