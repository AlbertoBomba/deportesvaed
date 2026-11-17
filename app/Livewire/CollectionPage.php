<?php

namespace App\Livewire;

use App\Support\StorefrontCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Models\Collection;

class CollectionPage extends Component
{
    use WithPagination;

    public Collection $collection;

    public function mount(string $slug): void
    {
        $this->collection = StorefrontCatalog::collections()
            ->whereHas('defaultUrl', fn (Builder $query) => $query->where('slug', $slug))
            ->firstOrFail();
    }

    public function render(): View
    {
        abort_unless(StorefrontCatalog::collections()->whereKey($this->collection->id)->exists(), 404);

        return view('livewire.collection-page', [
            'products' => StorefrontCatalog::products()
                ->whereHas('collections', fn (Builder $query) => $query->whereKey($this->collection->id))
                ->paginate(24),
        ])->layout('layouts.storefront');
    }
}
