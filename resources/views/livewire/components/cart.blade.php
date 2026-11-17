<div class="sm:relative" x-data="{ linesVisible: @entangle('linesVisible').live }" x-on:keydown.escape.window="linesVisible = false">
    <button type="button" class="relative flex size-11 items-center justify-center rounded-full border border-ink/15 transition-colors hover:bg-ink hover:text-white"
        x-on:click="linesVisible = !linesVisible" x-bind:aria-expanded="linesVisible" aria-controls="shopping-bag" aria-label="Abrir mi bolsa">
        <x-store-icon name="bag" />
        @if (count($lines))<span class="absolute -top-1 -right-1 flex min-w-5 items-center justify-center rounded-full bg-accent px-1 text-[10px] leading-5 font-bold text-white">{{ collect($lines)->sum('quantity') }}</span>@endif
    </button>
    <section id="shopping-bag" class="absolute top-full right-3 left-3 z-50 mt-3 max-h-[75dvh] overflow-y-auto rounded-2xl border border-ink/10 bg-paper p-6 shadow-2xl sm:top-auto sm:right-0 sm:left-auto sm:w-[390px]"
        x-show="linesVisible" x-on:click.outside="linesVisible = false" x-transition x-cloak aria-label="Mi bolsa">
        <div class="mb-6 flex items-center justify-between border-b border-ink/15 pb-5">
            <h2 class="text-xl font-extrabold tracking-tight">Tu bolsa<span class="text-accent">.</span></h2>
            <button type="button" class="flex size-9 items-center justify-center rounded-full hover:bg-ink/5" aria-label="Cerrar bolsa" x-on:click="linesVisible = false"><x-store-icon name="close" /></button>
        </div>
        @error('cart')<p class="mb-4 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
        @if ($this->cart && count($lines))
            <ul class="divide-y divide-ink/10">
                @foreach ($lines as $index => $line)
                    <li class="py-4 first:pt-0" wire:key="line_{{ $line['id'] }}">
                        <div class="flex gap-4">
                            <div class="flex h-24 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white">
                                @if ($line['thumbnail'])<img class="h-full w-full object-contain p-1" src="{{ $line['thumbnail'] }}" alt="{{ $line['description'] }}" />@else<x-store-icon name="bag" class="size-7 text-muted" />@endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold">{{ $line['description'] }}</p>
                                <p class="mt-1 text-xs text-muted">{{ $line['options'] }}</p>
                                <div class="mt-3 flex items-center gap-2">
                                    <label for="cart-quantity-{{ $line['id'] }}" class="sr-only">Cantidad de {{ $line['description'] }}</label>
                                    <input id="cart-quantity-{{ $line['id'] }}" class="w-16 rounded-lg border border-ink/15 bg-white p-2 text-xs" type="number" min="1" max="10000" step="1" wire:model.live="lines.{{ $index }}.quantity" />
                                    <span class="text-xs font-semibold">{{ $line['unit_price'] }}</span>
                                    <button class="ml-auto flex size-9 items-center justify-center text-muted hover:text-accent" type="button" wire:click="removeLine('{{ $line['id'] }}')" aria-label="Eliminar {{ $line['description'] }}"><x-store-icon name="trash" class="size-4" /></button>
                                </div>
                            </div>
                        </div>
                        @foreach ($errors->get('lines.'.$index.'.quantity') as $error)<p class="mt-3 text-xs text-red-700" role="alert">{{ $error }}</p>@endforeach
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 flex justify-between border-t border-ink/15 pt-5 text-sm font-bold"><dt>Subtotal</dt><dd>{{ $this->cart->subTotal->formatted() }}</dd></dl>
            <p class="mt-2 text-xs leading-5 text-muted">Envío e impuestos calculados al finalizar la compra.</p>
            <div class="mt-5 grid gap-3">
                <button class="button-secondary w-full" type="button" wire:click="updateLines" wire:loading.attr="disabled" wire:target="updateLines">Actualizar bolsa</button>
                <a class="button-primary w-full" href="{{ route('checkout.view') }}" wire:navigate>Finalizar compra <x-store-icon /></a>
            </div>
        @else
            <div class="flex flex-col items-center gap-4 py-8 text-center"><x-store-icon name="bag" class="size-12 text-muted" /><p class="font-bold">Tu bolsa espera su primer fichaje.</p><p class="text-sm text-muted">Encuentra algo que te mueva.</p></div>
        @endif
        <a class="mt-5 block text-center text-xs font-semibold underline underline-offset-4 hover:text-accent" href="{{ route('search.view') }}" wire:navigate>Seguir comprando</a>
    </section>
</div>
