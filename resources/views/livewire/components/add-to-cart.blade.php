<div>
    <div class="flex items-end gap-3">
        <div class="w-24 shrink-0">
            <label for="quantity-{{ $purchasable->id }}" class="mb-2 block text-xs font-semibold">Cantidad</label>
            <input class="min-h-13 w-full rounded-full border border-ink/20 bg-white px-3 py-3.5 text-center text-sm" type="number"
                id="quantity-{{ $purchasable->id }}" min="1" max="10000" step="1" wire:model.live="quantity" />
        </div>
        <button type="button" class="button-primary min-h-13 flex-1" wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart">
            <span wire:loading.remove wire:target="addToCart">Añadir a mi bolsa</span><span wire:loading wire:target="addToCart" role="status">Añadiendo…</span><x-store-icon name="bag" />
        </button>
    </div>
    @error('quantity')<p class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
</div>
