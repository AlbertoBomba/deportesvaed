<?php

namespace App\Livewire\Components;

use App\Support\StorefrontCatalog;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Lunar\Exceptions\Carts\CartException;
use Lunar\Facades\CartSession;
use Lunar\Models\ProductVariant;

class AddToCart extends Component
{
    /**
     * The purchasable model we want to add to the cart.
     */
    #[Locked]
    public ?ProductVariant $purchasable = null;

    /**
     * The quantity to add to cart.
     */
    public $quantity = 1;

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:10000',
        ];
    }

    public function addToCart(): void
    {
        $this->validate();

        abort_unless($this->purchasable && StorefrontCatalog::products()
            ->whereKey($this->purchasable->product_id)->exists(), 404);

        if (! StorefrontCatalog::price($this->purchasable, (int) $this->quantity)) {
            $this->addError('quantity', 'Price unavailable. Please contact the store.');

            return;
        }

        try {
            CartSession::manager()->add($this->purchasable, (int) $this->quantity);
        } catch (CartException $exception) {
            $this->addError('quantity', $exception->getMessage());

            return;
        }
        $this->dispatch('add-to-cart');
    }

    public function render(): View
    {
        return view('livewire.components.add-to-cart');
    }
}
