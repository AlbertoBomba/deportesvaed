<div class="bg-white border border-gray-100 rounded-xl">
    <div class="flex items-center h-16 px-6 border-b border-gray-100">
        <h3 class="text-lg font-medium">Payment</h3>
    </div>
    @if ($currentStep >= $step)
        <form wire:submit="checkout" class="p-6 space-y-4">
            <p class="p-4 text-sm text-blue-700 rounded-lg bg-blue-50">
                Payment is offline. No card details are collected and no online charge will be made.
            </p>
            <button class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 disabled:opacity-50"
                    type="submit" wire:loading.attr="disabled" wire:target="checkout">
                <span wire:loading.remove wire:target="checkout">Place order</span>
                <span wire:loading wire:target="checkout">Placing order...</span>
            </button>
        </form>
    @endif
</div>
