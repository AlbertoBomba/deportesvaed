<?php

namespace App\Livewire;

use App\Support\StorefrontCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPage extends Component
{
    public Product $product;

    /**
     * The selected option values.
     */
    public array $selectedOptionValues = [];

    public function mount(string $slug): void
    {
        $this->product = StorefrontCatalog::products()
            ->whereHas('defaultUrl', fn (Builder $query) => $query->where('slug', $slug))
            ->with(['media', 'variants.images', 'variants.values.option'])
            ->firstOrFail();

        $this->selectedOptionValues = $this->product->variants->first()?->values
            ->pluck('id', 'product_option_id')->all() ?? [];
    }

    /**
     * Computed property to get variant.
     */
    public function getVariantProperty(): ?ProductVariant
    {
        return $this->product->variants->first(function (ProductVariant $variant) {
            return $variant->values->pluck('id')->sort()->values()->all()
                === collect($this->selectedOptionValues)->map(fn ($value) => (int) $value)->sort()->values()->all();
        });
    }

    /**
     * Computed property to return all available option values.
     */
    public function getProductOptionValuesProperty(): Collection
    {
        return $this->product->variants->pluck('values')->flatten();
    }

    /**
     * Computed propert to get available product options with values.
     */
    public function getProductOptionsProperty(): Collection
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    /**
     * Return all images for the product.
     */
    public function getImagesProperty(): Collection
    {
        return $this->product->media->sortBy('order_column');
    }

    /**
     * Computed property to return current image.
     */
    public function getImageProperty(): ?Media
    {
        if ($this->variant?->images->isNotEmpty()) {
            return $this->variant->images->first();
        }

        if ($primary = $this->images->first(fn ($media) => $media->getCustomProperty('primary'))) {
            return $primary;
        }

        return $this->images->first();
    }

    public function render(): View
    {
        abort_unless(StorefrontCatalog::products()->whereKey($this->product->id)->exists(), 404);

        return view('livewire.product-page')->layout('layouts.storefront');
    }
}
