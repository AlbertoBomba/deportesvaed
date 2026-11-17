<?php

namespace App\Livewire;

use App\Support\StorefrontCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;

    #[Url]
    public string $term = '';

    public function updatedTerm(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->validate(['term' => ['string', 'max:200']]);
        $term = trim($this->term);

        return view('livewire.search-page', [
            'results' => StorefrontCatalog::products()
                ->when($term !== '', fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->whereLike('attribute_data->name->value', '%'.$term.'%')
                        ->orWhereHas('variants', fn (Builder $query) => $query->whereLike('sku', '%'.$term.'%'))
                ))
                ->orderBy('id')
                ->paginate(24),
        ])->layout('layouts.storefront');
    }
}
