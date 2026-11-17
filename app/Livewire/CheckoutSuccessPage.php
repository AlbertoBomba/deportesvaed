<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Order;

class CheckoutSuccessPage extends Component
{
    public function render(): View
    {
        $order = Order::query()->whereNotNull('placed_at')
            ->find(session('storefront.order_id'));

        if (! $order) {
            $this->redirectRoute('home');
        }

        return view('livewire.checkout-success-page', ['order' => $order])
            ->layout('layouts.checkout');
    }
}
