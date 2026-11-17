<div>
    <div class="store-container py-10 sm:py-14">
        <div class="mb-9">
            <p class="eyebrow text-accent">Un paso más hacia tu próxima meta</p>
            <h1 class="display-title mt-4 text-4xl sm:text-6xl">Ya casi es tuyo.</h1>
            <p class="mt-4 text-sm text-muted">Completa tus datos y revisa tu pedido antes de confirmarlo.</p>
        </div>
        @if ($errors->any())
            <ul class="mb-6 p-4 text-sm text-red-700 rounded-lg bg-red-50" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        @if ($cart && $cart->lines->isNotEmpty())
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-start">
            <div class="checkout-panel space-y-5 px-6 py-7 lg:sticky lg:top-8 lg:order-last">
                <h2 class="text-xl font-extrabold tracking-tight">
                    Tu equipación
                </h2>

                <div class="flow-root">
                    <div class="-my-4 divide-y divide-gray-100">
                        @foreach ($cart->lines as $line)
                            <div class="flex items-center py-4"
                                 wire:key="cart_line_{{ $line->id }}">
                                @if ($line->purchasable->getThumbnail())
                                    <img class="object-cover w-16 h-16 rounded"
                                         src="{{ $line->purchasable->getThumbnail()->getUrl() }}"
                                         alt="{{ $line->purchasable->getDescription() }}" />
                                @endif

                                <div class="flex-1 ml-4">
                                    <p class="text-sm font-medium max-w-[35ch]">
                                        {{ $line->purchasable->getDescription() }}
                                    </p>

                                    <span class="block mt-1 text-xs text-gray-500">
                                        {{ $line->quantity }} @ {{ $line->subTotal->formatted() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flow-root">
                    <dl class="-my-4 text-sm divide-y divide-gray-100">
                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium">
                                Subtotal
                            </dt>

                            <dd class="w-1/2 text-right">
                                {{ $cart->subTotal->formatted() }}
                            </dd>
                        </div>

                        @if ($this->shippingOption)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    {{ $this->shippingOption->getDescription() }}
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $this->shippingOption->getPrice()->formatted() }}
                                </dd>
                            </div>
                        @endif

                        @foreach ($cart->taxBreakdown->amounts as $tax)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    {{ $tax->description }}
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $tax->price->formatted() }}
                                </dd>
                            </div>
                        @endforeach

                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium">
                                Total
                            </dt>

                            <dd class="w-1/2 text-right text-lg font-bold">
                                {{ $cart->total->formatted() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                @if ($cart->isShippable())
                    @include('partials.checkout.address', [
                        'type' => 'shipping',
                        'step' => $steps['shipping_address'],
                    ])

                    @include('partials.checkout.shipping_option', [
                        'step' => $steps['shipping_option'],
                    ])
                @endif

                @include('partials.checkout.address', [
                    'type' => 'billing',
                    'step' => $steps['billing_address'],
                ])

                @include('partials.checkout.payment', [
                    'step' => $steps['payment'],
                ])
            </div>
        </div>
        @else
            <div class="empty-state"><x-store-icon name="bag" class="size-12" /><h2 class="text-xl font-bold text-ink">Tu bolsa está vacía.</h2><a href="{{ route('search.view') }}" class="button-primary" wire:navigate>Encuentra tu equipación <x-store-icon /></a></div>
        @endif
    </div>
</div>
