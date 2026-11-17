<?php

namespace App\Livewire;

use App\Support\StorefrontCatalog;
use Illuminate\View\View;
use Livewire\Component;

class Home extends Component
{
    public function render(): View
    {
        return view('livewire.home', [
            'products' => StorefrontCatalog::products()->latest('id')->limit(12)->get(),
        ])->layout('layouts.storefront');
    }
}
