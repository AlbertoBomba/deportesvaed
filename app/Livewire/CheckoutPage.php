<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Lunar\DataTypes\ShippingOption;
use Lunar\Exceptions\Carts\CartException;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\Country;

class CheckoutPage extends Component
{
    public array $shipping = [];

    public array $billing = [];

    public int $currentStep = 1;

    public bool $shippingIsBilling = true;

    public ?string $chosenShipping = null;

    #[Locked]
    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    public function mount(): void
    {
        if (! $this->cart || $this->cart->lines->isEmpty()) {
            $this->redirectRoute('home');

            return;
        }

        $this->shipping = $this->cart->shippingAddress?->only($this->addressFields()) ?? [];
        $this->billing = $this->cart->billingAddress?->only($this->addressFields()) ?? [];
        $this->determineCheckoutStep();
    }

    public function getCartProperty(): ?Cart
    {
        return CartSession::current();
    }

    public function determineCheckoutStep(): void
    {
        $this->currentStep = $this->cart->isShippable()
            ? $this->steps['shipping_address']
            : $this->steps['billing_address'];

        if ($this->cart->isShippable()) {
            if (! $this->cart->shippingAddress) {
                return;
            }

            $this->currentStep = $this->steps['shipping_option'];
            $this->chosenShipping = $this->shippingOption?->getIdentifier();

            if (! $this->shippingOption) {
                return;
            }

            $this->currentStep = $this->steps['billing_address'];
        }

        if ($this->cart->billingAddress) {
            $this->currentStep = $this->steps['payment'];
        }
    }

    public function saveAddress(string $type): void
    {
        abort_unless(in_array($type, ['shipping', 'billing'], true), 404);
        abort_unless($this->cart && $this->cart->lines->isNotEmpty(), 404);
        $validated = $this->validate($this->getAddressValidation($type))[$type];
        $cart = $this->cart;

        DB::transaction(function () use ($cart, $type, $validated): void {
            if ($type === 'shipping') {
                $cart->setShippingAddress([...$validated, 'shipping_option' => null]);

                if ($this->shippingIsBilling) {
                    $cart->setBillingAddress($validated);
                    $this->billing = $validated;
                }
            } else {
                $cart->setBillingAddress($validated);
            }
        });

        $this->refreshCart();
        $this->determineCheckoutStep();
    }

    public function saveShippingOption(): void
    {
        abort_unless($this->cart?->shippingAddress, 404);
        $option = $this->shippingOptions->first(
            fn (ShippingOption $option) => $option->getIdentifier() === $this->chosenShipping
        );

        if (! $option) {
            throw ValidationException::withMessages([
                'chosenShipping' => 'Select an available shipping method.',
            ]);
        }

        try {
            $this->cart->setShippingOption($option);
        } catch (CartException $exception) {
            $this->addError('chosenShipping', $exception->getMessage());

            return;
        }

        $this->refreshCart();
        $this->determineCheckoutStep();
    }

    public function checkout(): void
    {
        $cart = $this->cart;
        abort_unless($cart && $cart->lines->isNotEmpty(), 404);

        try {
            $orderId = DB::transaction(function () use ($cart): int {
                $cart = Cart::query()->lockForUpdate()->findOrFail($cart->id);
                abort_if($cart->completedOrders()->exists(), 409);

                foreach (['billing', ...($cart->isShippable() ? ['shipping'] : [])] as $type) {
                    $address = $type === 'billing' ? $cart->billingAddress : $cart->shippingAddress;
                    Validator::make(
                        [$type => $address?->only($this->addressFields()) ?? []],
                        $this->getAddressValidation($type)
                    )->validate();
                }

                if ($cart->isShippable() && ! $cart->getShippingOption()) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Select an available shipping method before placing your order.',
                    ]);
                }

                // Re-run Lunar's stock and availability validators, including resumed draft orders.
                $cart->updateLines($cart->lines->map(fn ($line) => [
                    'id' => $line->id,
                    'quantity' => $line->quantity,
                ]));

                $order = $cart->createOrder();
                $payment = Payments::driver('cash-in-hand')->order($order)->authorize();

                if (! $payment?->success || ! $payment->orderId) {
                    throw ValidationException::withMessages([
                        'checkout' => 'Your order could not be placed. Please try again.',
                    ]);
                }

                return $payment->orderId;
            });
        } catch (CartException $exception) {
            $this->addError('checkout', $exception->getMessage());

            return;
        }

        session()->put('storefront.order_id', $orderId);
        CartSession::forget(delete: false);
        $this->redirectRoute('checkout-success.view');
    }

    public function getCountriesProperty(): Collection
    {
        return Country::query()->orderBy('name')->get();
    }

    public function getShippingOptionsProperty(): Collection
    {
        return $this->cart ? ShippingManifest::getOptions($this->cart) : collect();
    }

    public function getShippingOptionProperty(): ?ShippingOption
    {
        return $this->cart?->getShippingOption();
    }

    protected function getAddressValidation(string $type): array
    {
        return [
            "{$type}.first_name" => ['required', 'string', 'max:255'],
            "{$type}.last_name" => ['required', 'string', 'max:255'],
            "{$type}.line_one" => ['required', 'string', 'max:255'],
            "{$type}.country_id" => ['required', 'integer', Rule::exists(Country::class, 'id')],
            "{$type}.city" => ['required', 'string', 'max:255'],
            "{$type}.postcode" => ['required', 'string', 'max:50'],
            "{$type}.company_name" => ['nullable', 'string', 'max:255'],
            "{$type}.line_two" => ['nullable', 'string', 'max:255'],
            "{$type}.line_three" => ['nullable', 'string', 'max:255'],
            "{$type}.state" => ['nullable', 'string', 'max:255'],
            "{$type}.contact_email" => ['required', 'email', 'max:255'],
            "{$type}.contact_phone" => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function addressFields(): array
    {
        return [
            'first_name', 'last_name', 'line_one', 'line_two', 'line_three',
            'country_id', 'city', 'postcode', 'company_name', 'state',
            'contact_email', 'contact_phone',
        ];
    }

    protected function refreshCart(): void
    {
        $this->cart?->refresh();
        unset($this->cart, $this->shippingOption, $this->shippingOptions);
    }

    public function render(): View
    {
        return view('livewire.checkout-page', ['cart' => $this->cart])
            ->layout('layouts.checkout');
    }
}
