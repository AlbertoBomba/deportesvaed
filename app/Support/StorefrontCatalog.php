<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Lunar\Facades\Pricing;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Collection;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class StorefrontCatalog
{
    public static function products(): Builder
    {
        return self::visible(Product::query())
            ->where('status', 'published')
            ->whereHas('defaultUrl')
            ->with(['defaultUrl', 'thumbnail', 'variants.prices.currency']);
    }

    public static function collections(): Builder
    {
        return self::visible(Collection::query())
            ->whereHas('defaultUrl')
            ->with('defaultUrl');
    }

    public static function price(ProductVariant $variant, int $quantity = 1): ?Price
    {
        $currency = StorefrontSession::getCurrency();
        $groups = StorefrontSession::getCustomerGroups();
        $hasPrice = $variant->getPrices()->contains(
            fn (Price $price) => $price->currency_id === $currency->id
                && $price->min_quantity <= $quantity
                && (! $price->customer_group_id || $groups->contains('id', $price->customer_group_id))
        );

        if (! $hasPrice) {
            return null;
        }

        return Pricing::for($variant)->currency($currency)->customerGroups($groups)
            ->qty($quantity)->get()->matched;
    }

    private static function visible(Builder $query): Builder
    {
        $groups = StorefrontSession::getCustomerGroups();

        return $query->channel(StorefrontSession::getChannel())
            ->customerGroup($groups)
            ->when($groups->isEmpty(), fn (Builder $query) => $query->whereRaw('1 = 0'));
    }
}
