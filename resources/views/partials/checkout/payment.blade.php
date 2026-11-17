<div class="checkout-panel">
    <div class="flex items-center h-16 px-6 border-b border-gray-100">
        <h2 class="flex items-center gap-3 text-base font-bold"><span class="flex size-8 items-center justify-center rounded-full bg-paper text-xs text-accent">{{ $step }}</span> Confirmación y pago</h2>
    </div>
    @if ($currentStep >= $step)
        <form wire:submit="checkout" class="p-6 space-y-4">
            <p class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm leading-6 text-orange-900">
                El pago se realiza fuera de la web. No te pediremos datos de tarjeta ni se realizará ningún cargo online. Tu pedido quedará pendiente de pago.
            </p>
            <button class="button-primary w-full sm:w-auto"
                    type="submit" wire:loading.attr="disabled" wire:target="checkout">
                <span wire:loading.remove wire:target="checkout">Confirmar pedido</span>
                <span wire:loading wire:target="checkout" role="status">Confirmando pedido…</span>
                <x-store-icon class="size-4" />
            </button>
        </form>
    @endif
</div>
