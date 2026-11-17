<?php

namespace Tests\Feature;

use App\Livewire\CheckoutPage;
use App\Livewire\Components\AddToCart;
use App\Livewire\Components\Cart as CartComponent;
use App\Livewire\ProductPage;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Lunar\DataTypes\Price as PriceValue;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\CartSession;
use Lunar\Facades\ShippingManifest;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxZone;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_catalog_shows_only_published_available_products(): void
    {
        $this->prepareStorefront();
        $visible = $this->variant('Visible shoes');
        $this->variant('Draft shoes')->product->update(['status' => 'draft']);
        $hidden = $this->variant('Hidden shoes')->product;
        $hidden->channels()->updateExistingPivot(Channel::getDefault()->id, ['enabled' => false]);
        $private = $this->variant('Private shoes')->product;
        $private->customerGroups()->updateExistingPivot(CustomerGroup::getDefault()->id, [
            'enabled' => false, 'visible' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible shoes')
            ->assertDontSee('Draft shoes')
            ->assertDontSee('Hidden shoes')
            ->assertDontSee('Private shoes');
        $this->get(route('product.view', $visible->product->defaultUrl->slug))->assertOk();
        $this->get(route('product.view', $hidden->defaultUrl->slug))->assertNotFound();
        $this->get(route('product.view', $private->defaultUrl->slug))->assertNotFound();
    }

    public function test_search_finds_translated_names_and_skus_without_a_search_service(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant('Running shoes');
        $variant->product->update(['attribute_data' => [
            'name' => new TranslatedText(['en' => 'Running shoes', 'es' => 'Zapatillas']),
        ]]);
        $variant->update(['sku' => 'SPORT-123']);
        $this->variant('Tennis racket');
        $this->variant('Draft running shoes')->product->update(['status' => 'draft']);

        $this->get('/search?term=Running')
            ->assertOk()->assertSee('Running shoes')->assertDontSee('Tennis racket')
            ->assertDontSee('Draft running shoes');
        $this->get('/search?term=SPORT-123')->assertOk()->assertSee('Running shoes');
        $this->get('/search?term=Zapatillas')->assertOk()->assertSee('Running shoes');
        $this->get('/search?term=missing')->assertOk()->assertSee('No hemos encontrado productos.');
    }

    public function test_collection_lists_only_available_products_and_hides_unavailable_collections(): void
    {
        $this->prepareStorefront();
        $collection = Collection::factory()->create(['attribute_data' => ['name' => new Text('Sports')]]);
        $collection->products()->attach($this->variant('Visible ball')->product);
        $draft = $this->variant('Draft ball')->product;
        $draft->update(['status' => 'draft']);
        $collection->products()->attach($draft);

        $this->get(route('collection.view', $collection->defaultUrl->slug))
            ->assertOk()->assertSee('Visible ball')->assertDontSee('Draft ball');

        $collection->channels()->updateExistingPivot(Channel::getDefault()->id, ['enabled' => false]);
        $this->get(route('collection.view', $collection->defaultUrl->slug))->assertNotFound();
    }

    public function test_missing_catalog_urls_return_not_found(): void
    {
        $this->prepareStorefront();

        $this->get('/products/missing')->assertNotFound();
        $this->get('/collections/missing')->assertNotFound();
        $this->get('/search')->assertOk()->assertSee('No hemos encontrado productos.');
    }

    public function test_incomplete_products_and_missing_option_combinations_do_not_crash(): void
    {
        $this->prepareStorefront();
        $product = Product::factory()->create(['attribute_data' => ['name' => new Text('Coming soon')]]);
        $this->get(route('product.view', $product->defaultUrl->slug))
            ->assertOk()->assertSee('Esta combinación no está disponible.');

        $variant = $this->variant('Option shoes');
        $option = ProductOption::factory()->create();
        $value = ProductOptionValue::factory()->create(['product_option_id' => $option->id]);
        $variant->values()->attach($value);

        Livewire::test(ProductPage::class, ['slug' => $variant->product->defaultUrl->slug])
            ->set('selectedOptionValues', [$option->id => 99999])
            ->assertSee('Esta combinación no está disponible.');
    }

    #[TestWith([[], '', '', ''])]
    #[TestWith([['medium' => true], 'medium', 'medium', ''])]
    #[TestWith([['large' => true, 'medium' => true, 'small' => true], 'large', 'medium', 'small'])]
    public function test_product_images_use_available_conversions_or_the_original(
        array $generatedConversions,
        string $mainConversion,
        string $cardConversion,
        string $thumbnailConversion,
    ): void {
        $this->prepareStorefront();
        Storage::fake('public');
        Queue::fake([PerformConversionsJob::class]);
        config([
            'media-library.queue_conversions_by_default' => true,
            'media-library.queue_conversions_after_database_commit' => false,
        ]);
        $product = $this->variant()->product;
        $image = $product->addMedia(UploadedFile::fake()->image('running-shoes.png'))
            ->withCustomProperties(['primary' => true])
            ->toMediaCollection('images', 'public');
        $image->update(['generated_conversions' => $generatedConversions]);

        $this->get(route('product.view', $product->defaultUrl->slug))
            ->assertOk()
            ->assertSeeInOrder([
                'src="'.$image->getUrl($mainConversion).'"',
                'aria-label="Imágenes del producto"',
                'src="'.$image->getUrl($thumbnailConversion).'"',
            ], false);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('src="'.$image->getUrl($cardConversion).'"', false);

        Storage::disk('public')->assertExists($image->getPathRelativeToRoot());
        Queue::assertPushed(PerformConversionsJob::class);
    }

    public function test_adding_a_product_uses_lunar_inventory_rules_and_session_cart(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->update(['stock' => 0, 'purchasable' => 'always']);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->set('quantity', 2)->call('addToCart')
            ->assertHasNoErrors()->assertDispatched('add-to-cart');

        $this->assertDatabaseHas('lunar_cart_lines', [
            'cart_id' => CartSession::current()->id,
            'purchasable_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    #[TestWith([0])]
    #[TestWith([1.5])]
    #[TestWith([10001])]
    public function test_invalid_cart_quantities_are_rejected(int|float $quantity): void
    {
        $this->prepareStorefront();

        Livewire::test(AddToCart::class, ['purchasable' => $this->variant()])
            ->set('quantity', $quantity)->call('addToCart')->assertHasErrors('quantity');
        $this->assertDatabaseCount('lunar_cart_lines', 0);
    }

    public function test_stock_failure_is_shown_without_adding_a_line(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->update(['stock' => 1, 'purchasable' => 'in_stock']);

        Livewire::test(AddToCart::class, ['purchasable' => $variant])
            ->set('quantity', 2)->call('addToCart')->assertHasErrors('quantity');
        $this->assertDatabaseCount('lunar_cart_lines', 0);
    }

    public function test_missing_prices_are_visible_and_cannot_create_a_cart_line(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->prices()->delete();

        $this->get(route('product.view', $variant->product->defaultUrl->slug))
            ->assertOk()->assertSee('Precio no disponible');
        Livewire::test(AddToCart::class, ['purchasable' => $variant->fresh()])
            ->call('addToCart')->assertHasErrors('quantity')
            ->assertSee('Price unavailable. Please contact the store.');
        $this->assertDatabaseCount('lunar_cart_lines', 0);
    }

    public function test_cart_cannot_remove_another_sessions_line(): void
    {
        $this->prepareStorefront();
        CartSession::manager()->add($this->variant());
        $foreign = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
        ])->add($this->variant('Other shoes'))->lines->first();

        Livewire::test(CartComponent::class)->call('removeLine', $foreign->id)->assertNotFound();
        $this->assertDatabaseHas('lunar_cart_lines', ['id' => $foreign->id, 'quantity' => 1]);
    }

    public function test_cart_updates_and_removes_its_own_lines(): void
    {
        $this->prepareStorefront();
        $cart = CartSession::manager()->add($this->variant());
        $line = $cart->lines->first();

        $component = Livewire::test(CartComponent::class)
            ->set('lines.0.quantity', 3)->call('updateLines')->assertHasNoErrors()
            ->assertSee('Finalizar compra');
        $this->assertDatabaseHas('lunar_cart_lines', ['id' => $line->id, 'quantity' => 3]);

        $component->call('removeLine', $line->id)
            ->assertSee('Tu bolsa espera su primer fichaje.')
            ->assertDontSee('Finalizar compra');
        $this->assertDatabaseMissing('lunar_cart_lines', ['id' => $line->id]);
    }

    public function test_cart_cannot_update_another_sessions_line(): void
    {
        $this->prepareStorefront();
        CartSession::manager()->add($this->variant());
        $foreign = Cart::factory()->create([
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
        ])->add($this->variant('Other shoes'))->lines->first();

        Livewire::test(CartComponent::class)
            ->set('lines.0.id', $foreign->id)
            ->set('lines.0.quantity', 5)
            ->call('updateLines')->assertNotFound();
        $this->assertDatabaseHas('lunar_cart_lines', ['id' => $foreign->id, 'quantity' => 1]);
    }

    public function test_checkout_and_confirmation_redirect_without_a_cart_or_order(): void
    {
        $this->prepareStorefront();

        $this->get('/checkout')->assertRedirect(route('home'));
        $this->get('/checkout/success')->assertRedirect(route('home'));
        $this->get('/lunar/login')->assertOk();
    }

    public function test_checkout_validates_addresses_and_does_not_accept_model_attributes(): void
    {
        $this->prepareStorefront();
        $cart = CartSession::manager()->add($this->variant());
        $component = Livewire::test(CheckoutPage::class)
            ->call('saveAddress', 'shipping')
            ->assertHasErrors(['shipping.first_name', 'shipping.country_id', 'shipping.contact_email']);

        $component->set('shipping', [...$this->address(), 'cart_id' => 9999, 'shipping_option' => 'forged'])
            ->call('saveAddress', 'shipping')->assertHasNoErrors();

        $cart->refresh();
        $this->assertSame('Ada', $cart->shippingAddress->first_name);
        $this->assertSame('Ada', $cart->billingAddress->first_name);
        $this->assertNull($cart->shippingAddress->shipping_option);
        $this->assertSame($cart->id, $cart->shippingAddress->cart_id);
    }

    public function test_physical_checkout_without_shipping_is_explicitly_blocked(): void
    {
        $this->prepareStorefront();
        CartSession::manager()->add($this->variant());

        Livewire::test(CheckoutPage::class)
            ->set('shipping', $this->address())->call('saveAddress', 'shipping')
            ->assertSee('No hay métodos de envío disponibles')
            ->set('chosenShipping', 'forged')->call('saveShippingOption')
            ->assertHasErrors('chosenShipping')
            ->call('checkout')->assertHasErrors('checkout');

        $this->assertDatabaseCount('lunar_orders', 0);
    }

    public function test_checkout_cannot_skip_persisted_address_validation(): void
    {
        $this->prepareStorefront();
        CartSession::manager()->add($this->variant());

        Livewire::test(CheckoutPage::class)
            ->set('currentStep', 4)->call('checkout')->assertHasErrors('billing.first_name');
        $this->assertDatabaseCount('lunar_orders', 0);
    }

    public function test_offline_order_uses_existing_shipping_and_confirmation_is_session_scoped(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        CartSession::manager()->add($variant);
        ShippingManifest::addOption(new ShippingOption(
            name: 'Existing delivery',
            description: 'Existing delivery',
            identifier: 'existing',
            price: new PriceValue(500, Currency::getDefault(), 1),
            taxClass: TaxClass::getDefault(),
        ));

        Livewire::test(CheckoutPage::class)
            ->set('shipping', $this->address())->call('saveAddress', 'shipping')
            ->set('chosenShipping', 'existing')->call('saveShippingOption')
            ->assertSet('currentStep', 4)
            ->call('checkout')->assertHasNoErrors()
            ->assertRedirect(route('checkout-success.view'));

        $order = Order::query()->sole();
        $this->assertSame('payment-offline', $order->status);
        $this->assertNotNull($order->placed_at);
        $this->assertSame(2500, $order->total->value);
        $this->assertDatabaseHas('lunar_order_lines', [
            'order_id' => $order->id, 'identifier' => $variant->sku, 'quantity' => 1,
        ]);
        $this->get('/checkout/success')->assertOk()->assertSee($order->reference)
            ->assertSee('Pago pendiente')
            ->assertSee('No se ha realizado ningún cargo online.');
        $this->get('/checkout/success')->assertOk()->assertSee($order->reference);
        $this->get('/checkout')->assertRedirect(route('home'));
        $this->assertDatabaseCount('lunar_orders', 1);
        session()->forget('storefront.order_id');
        $this->get('/checkout/success?order_id='.$order->id)->assertRedirect(route('home'));
    }

    public function test_digital_order_does_not_require_shipping(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->update(['shippable' => false]);
        CartSession::manager()->add($variant);

        Livewire::test(CheckoutPage::class)
            ->assertSet('currentStep', 3)
            ->set('billing', $this->address())->call('saveAddress', 'billing')
            ->call('checkout')->assertHasNoErrors()
            ->assertRedirect(route('checkout-success.view'));
        $this->assertDatabaseCount('lunar_orders', 1);
    }

    public function test_resuming_a_draft_uses_the_current_cart_quantities_and_totals(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->update(['shippable' => false]);
        $cart = CartSession::manager()->add($variant);
        $cart->setBillingAddress($this->address());
        $draft = $cart->createOrder();
        $cart->updateLine($cart->lines->first()->id, 3);

        Livewire::test(CheckoutPage::class)->call('checkout')->assertHasNoErrors();

        $this->assertDatabaseCount('lunar_orders', 1);
        $this->assertSame(6000, $draft->fresh()->total->value);
        $this->assertDatabaseHas('lunar_order_lines', ['order_id' => $draft->id, 'quantity' => 3]);
    }

    public function test_checkout_rechecks_stock_before_placing_an_order(): void
    {
        $this->prepareStorefront();
        $variant = $this->variant();
        $variant->update(['shippable' => false]);
        CartSession::manager()->add($variant);
        $component = Livewire::test(CheckoutPage::class)
            ->set('billing', $this->address())->call('saveAddress', 'billing');
        $variant->update(['stock' => 0, 'purchasable' => 'in_stock']);

        $component->call('checkout')->assertHasErrors('checkout');
        $this->assertDatabaseCount('lunar_orders', 0);
    }

    private function prepareStorefront(): void
    {
        config(['scout.driver' => null]);
        app()->setLocale('en');
        Channel::factory()->create(['default' => true]);
        Currency::factory()->create(['code' => 'EUR', 'default' => true, 'exchange_rate' => 1]);
        CustomerGroup::factory()->create(['default' => true]);
        Language::factory()->create(['code' => 'en', 'default' => true]);
        Country::factory()->create(['name' => 'Spain', 'iso2' => 'ES', 'iso3' => 'ESP']);
        TaxClass::factory()->create(['default' => true]);
        TaxZone::factory()->create([
            'default' => true, 'zone_type' => 'country', 'price_display' => 'tax_inclusive',
        ]);
    }

    private function variant(string $name = 'Running shoes'): ProductVariant
    {
        $product = Product::factory()->create(['attribute_data' => [
            'name' => new Text($name), 'description' => new Text('Sports equipment'),
        ]]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'tax_class_id' => TaxClass::getDefault()->id,
            'stock' => 10,
            'purchasable' => 'in_stock',
        ]);
        Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => Currency::getDefault()->id,
            'price' => 2000,
            'min_quantity' => 1,
        ]);

        return $variant;
    }

    private function address(): array
    {
        return [
            'first_name' => 'Ada', 'last_name' => 'Lovelace',
            'line_one' => 'Example street 1', 'city' => 'Madrid', 'postcode' => '28001',
            'country_id' => Country::query()->firstOrFail()->id,
            'contact_email' => 'customer@example.com',
        ];
    }
}
