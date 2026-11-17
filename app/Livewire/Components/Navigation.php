<?php

namespace App\Livewire\Components;

use App\Support\StorefrontCatalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class Navigation extends Component
{
    /**
     * The search term for the search input.
     *
     * @var string
     */
    public $term = null;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
    ];

    /**
     * Return the collections in a tree.
     */
    public function getCollectionsProperty(): Collection
    {
        return StorefrontCatalog::collections()->orderBy('_lft')->get();
    }

    public function render(): View
    {
        return view('livewire.components.navigation');
    }
}
