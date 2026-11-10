<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryShippingTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Teszt Vásárló', 'email' => 'customer@example.com', 'phone' => '12345678',
            'address' => 'Eger, Teszt utca 1.', 'payment_method' => 'cod', 'shipping_method' => 'courier',
        ], $overrides);
    }

    private function cart(User $user, Product $product, int $quantity = 1): void
    {
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => $quantity]);
    }

    public function test_guest_and_customer_carts_cannot_exceed_stock(): void
    {
        $product = Product::factory()->create(['stock' => 1]);
        $this->postJson('/cart/add/'.$product->id)->assertOk();
        $this->postJson('/cart/increase/'.$product->id)->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertEquals(1, session('cart')[$product->id]['quantity']);
        $this->actingAs(User::factory()->create())->postJson('/cart/add/'.$product->id)->assertOk();
        $this->postJson('/cart/add/'.$product->id)->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertEquals(1, CartItem::firstOrFail()->quantity);
        $product->update(['stock' => 0]);
        $this->get('/cart')->assertOk()->assertSee('Az adott mennyiség nincs készleten.')->assertSee('Jelenleg 0 db van készleten.');
        $this->postJson('/cart/remove/'.$product->id)->assertOk();
    }

    public function test_sold_out_product_cannot_be_added_and_has_disabled_button(): void
    {
        $product = Product::factory()->create(['stock' => 0]);
        $this->postJson('/cart/add/'.$product->id)->assertUnprocessable();
        $this->get('/product/'.$product->id)->assertOk()->assertSee('Elfogyott')->assertSee('disabled', false);
    }

    public function test_guest_merge_caps_quantity_and_warns_customer(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);
        $this->cart($user, $product, 2);
        app(CartService::class)->mergeGuestCart($user, [$product->id => ['quantity' => 4]]);
        $this->assertEquals(3, CartItem::firstOrFail()->quantity);
        $this->assertNotEmpty(session('error'));
    }

    public function test_checkout_uses_server_shipping_fee_and_reserves_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'price' => 1000, 'discount' => 10]);
        $this->cart($user, $product, 2);
        $this->actingAs($user)->get('/checkout')->assertOk()->assertSee('Házhoz szállítás')->assertSee('1 990');
        $this->post('/order/store', $this->data(['shipping_fee' => 1, 'total' => 1, 'stock_reserved' => false]))->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $this->assertEquals(3790, $order->total);
        $this->assertEquals(1990, $order->shipping_fee);
        $this->assertEquals(3, $product->fresh()->stock);
        $this->assertTrue((bool) $order->stock_reserved);
        config(['shipping.methods.courier.fee' => 4990, 'shipping.methods.courier.label' => 'Új díjszabás']);
        $this->get('/orders/'.$order->id)->assertSee('1 990 Ft')->assertSee('Házhoz szállítás')->assertDontSee('Új díjszabás');
        $this->post('/order/store', $this->data())->assertSessionHasErrors('cart');
        $this->assertEquals(3, $product->fresh()->stock);
    }

    public function test_pickup_is_free_and_unknown_shipping_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'price' => 1000, 'discount' => 0]);
        $this->cart($user, $product);
        $this->actingAs($user)->post('/order/store', $this->data(['shipping_method' => 'free']))->assertSessionHasErrors('shipping_method');
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(5, $product->fresh()->stock);
        $this->post('/order/store', $this->data(['shipping_method' => 'pickup']))->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $this->assertEquals(1000, $order->total);
        $this->assertEquals(0, $order->shipping_fee);
        $this->assertSame(config('shipping.pickup_address'), $order->pickup_address);
    }

    public function test_two_customers_cannot_purchase_the_same_last_unit(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);
        $this->cart($first, $product);
        $this->cart($second, $product);
        $this->actingAs($first)->post('/order/store', $this->data())->assertSessionHasNoErrors();
        $this->actingAs($second)->post('/order/store', $this->data())->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('cart_items', ['user_id' => $second->id, 'quantity' => 1]);
        $this->assertEquals(0, $product->fresh()->stock);
    }

    public function test_insufficient_stock_or_write_failure_leaves_all_stock_and_cart_unchanged(): void
    {
        $user = User::factory()->create();
        $first = Product::factory()->create(['stock' => 2]);
        $second = Product::factory()->create(['stock' => 0]);
        $this->cart($user, $first);
        $this->cart($user, $second);
        $this->actingAs($user)->post('/order/store', $this->data())->assertSessionHasErrors('cart');
        $this->assertEquals(2, $first->fresh()->stock);
        $second->update(['stock' => 1]);
        OrderItem::creating(fn () => throw new \RuntimeException('Write failure'));
        $this->withoutExceptionHandling();
        try {
            $this->post('/order/store', $this->data());
            $this->fail('Expected failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('Write failure', $e->getMessage());
        } finally {
            OrderItem::flushEventListeners();
        }
        $this->assertEquals(2, $first->fresh()->stock);
        $this->assertEquals(1, $second->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 2);
    }

    public function test_cancel_restores_stock_once_and_reactivation_requires_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2]);
        $this->cart($user, $product, 2);
        $this->actingAs($user)->post('/order/store', $this->data())->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $url = '/admin/orders/'.$order->id.'/status';
        $this->patch($url, ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->patch($url, ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertEquals(2, $product->fresh()->stock);
        $this->put('/admin/orders/'.$order->id, $this->data(['status' => 'processing']))->assertSessionHasNoErrors();
        $this->assertEquals(0, $product->fresh()->stock);
        $this->patch($url, ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $product->update(['stock' => 1]);
        $this->patch($url, ['status' => 'processing'])->assertSessionHasErrors('status');
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertEquals(1, $product->fresh()->stock);
    }

    public function test_legacy_orders_never_restore_unreserved_stock(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);
        $order->items()->create(['product_id' => $product->id, 'quantity' => 2, 'price' => 1000]);
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->patch('/admin/orders/'.$order->id.'/status', ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertEquals(3, $product->fresh()->stock);
    }

    public function test_admin_can_update_stock_but_invalid_values_are_rejected(): void
    {
        $category = Category::create(['name' => 'Laptop']);
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 3]);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $data = ['name' => $product->name, 'price' => 1000, 'category_id' => $category->id, 'stock' => 7];
        $this->put('/admin/products/'.$product->id, $data)->assertSessionHasNoErrors();
        $this->assertEquals(7, $product->fresh()->stock);
        foreach ([-1, 1.5, 1000001] as $stock) {
            $this->put('/admin/products/'.$product->id, array_replace($data, ['stock' => $stock]))->assertSessionHasErrors('stock');
        }
        $this->assertEquals(7, $product->fresh()->stock);
    }
}
