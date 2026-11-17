<?php

namespace App\Livewire\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Exceptions\Carts\CartException;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart as CartModel;

class Cart extends Component
{
    /**
     * The editable cart lines.
     */
    public array $lines = [];

    public bool $linesVisible = false;

    protected $listeners = [
        'add-to-cart' => 'handleAddToCart',
    ];

    public function rules(): array
    {
        return [
            'lines.*.id' => 'required|integer',
            'lines.*.quantity' => 'required|integer|min:1|max:10000',
        ];
    }

    public function mount(): void
    {
        $this->mapLines();
    }

    /**
     * Get the current cart instance.
     */
    public function getCartProperty(): ?CartModel
    {
        return CartSession::current();
    }

    /**
     * Return the cart lines from the cart.
     */
    public function getCartLinesProperty(): Collection
    {
        return $this->cart->lines ?? collect();
    }

    /**
     * Update the cart lines.
     */
    public function updateLines(): void
    {
        $this->validate();

        $cart = $this->cart;
        abort_unless($cart, 404);
        $lines = collect($this->lines)->map(function (array $line) use ($cart): array {
            abort_unless($cart->lines->contains('id', $line['id']), 404);

            return ['id' => (int) $line['id'], 'quantity' => (int) $line['quantity']];
        });

        try {
            $cart->updateLines($lines);
        } catch (CartException $exception) {
            $this->addError('cart', $exception->getMessage());

            return;
        }

        $this->mapLines();
        $this->dispatch('cartUpdated');
    }

    public function removeLine(int $id): void
    {
        $cart = $this->cart;
        abort_unless($cart && $cart->lines->contains('id', $id), 404);
        $cart->remove($id);
        $this->mapLines();
        $this->dispatch('cartUpdated');
    }

    /**
     * Map the cart lines.
     *
     * We want to map out our cart lines like this so we can
     * add some validation rules and make them editable.
     */
    public function mapLines(): void
    {
        $this->lines = $this->cartLines->map(function ($line) {
            return [
                'id' => $line->id,
                'identifier' => $line->purchasable->getIdentifier(),
                'quantity' => $line->quantity,
                'description' => $line->purchasable->getDescription(),
                'thumbnail' => $line->purchasable->getThumbnail()?->getUrl(),
                'option' => $line->purchasable->getOption(),
                'options' => $line->purchasable->getOptions()->implode(' / '),
                'sub_total' => $line->subTotal->formatted(),
                'unit_price' => $line->unitPrice->formatted(),
            ];
        })->toArray();
    }

    public function handleAddToCart(): void
    {
        $this->mapLines();
        $this->linesVisible = true;
    }

    public function render(): View
    {
        return view('livewire.components.cart');
    }
}
