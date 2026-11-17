<?php

namespace App\View\Components;

use App\Support\StorefrontCatalog;
use Illuminate\View\Component;
use Illuminate\View\View;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class ProductPrice extends Component
{
    public ?Price $price = null;

    public ?ProductVariant $variant = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?Product $product = null, ?ProductVariant $variant = null)
    {
        $purchasable = $variant ?? $product?->variants->first();

        if ($purchasable) {
            $this->price = StorefrontCatalog::price($purchasable);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.product-price');
    }
}
