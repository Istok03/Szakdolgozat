<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_must_log_in_to_all_customer_order_pages(): void
    {
        foreach (['/orders', '/orders/1', '/orders/1/confirmation'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_list_is_paginated_and_contains_only_own_orders_newest_first(): void
    {
        $customer = User::factory()->create();
        $own = Order::factory()->count(11)->create(['user_id' => $customer->id]);
        $other = Order::factory()->create();
        $response = $this->actingAs($customer)->get('/orders')->assertOk()->assertSee('Következő');
        $orders = $response->viewData('orders');
        $this->assertEquals(11, $orders->total());
        $this->assertCount(10, $orders);
        $this->assertTrue($own->last()->is($orders->first()));
        $this->assertFalse($orders->contains('id', $other->id));
        $second = $this->get('/orders?page=2')->assertOk()->viewData('orders');
        $this->assertCount(1, $second);
        $this->assertTrue($own->first()->is($second->first()));
    }

    public function test_empty_list_has_a_shopping_link(): void
    {
        $this->actingAs(User::factory()->create())->get('/orders')->assertOk()
            ->assertSee('Még nincs rendelésed')->assertSee(route('products'));
    }

    public function test_other_missing_and_orphaned_orders_are_not_accessible_even_to_an_admin(): void
    {
        $other = Order::factory()->create();
        $orphan = Order::factory()->create(['user_id' => null]);
        foreach ([false, true] as $admin) {
            $this->actingAs(User::factory()->create(['is_admin' => $admin]));
            foreach ([$other->id, $orphan->id, 999999] as $id) {
                $this->get('/orders/'.$id)->assertNotFound();
                $this->withSession(['completed_order_id' => $id])->get('/orders/'.$id.'/confirmation')->assertNotFound();
            }
        }
    }

    public function test_details_use_historical_values_and_handle_deleted_products_and_current_status(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id, 'total' => 2468, 'status' => 'shipped']);
        $product = Product::factory()->create(['name' => 'Új terméknév', 'price' => 99999]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => 'Eredeti terméknév', 'quantity' => 2, 'price' => 1234]);
        $this->actingAs($customer)->get('/orders/'.$order->id)->assertOk()->assertSee('Eredeti terméknév')
            ->assertSee('1 234 Ft')->assertSee('2 468 Ft')->assertSee('Feladva')
            ->assertDontSee('Új terméknév')->assertDontSee('99 999 Ft')->assertSee(route('product.show', $product));
        $product->delete();
        $this->get('/orders/'.$order->id)->assertOk()->assertSee('Eredeti terméknév')
            ->assertSee('A termék már nem érhető el')->assertDontSee('Jelenlegi termékoldal');
    }

    public function test_successful_checkout_redirects_to_confirmation_and_refresh_does_not_duplicate_order(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'discount' => 10]);
        CartItem::create(['user_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 2]);
        $this->actingAs($customer)->post(route('order.store'), [
            'name' => 'Vásárló', 'email' => $customer->email, 'phone' => '12345678',
            'address' => 'Eger, Teszt utca 1.', 'payment_method' => 'cod',
        ])->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $this->get(route('orders.confirmation', $order))->assertOk()->assertSee('Köszönjük a rendelésed!')
            ->assertSee('1 800 Ft')->assertSee('Utánvét')->assertSee('Eger, Teszt utca 1.');
        $this->get(route('orders.confirmation', $order))->assertRedirect(route('orders.show', $order));
        $this->get(route('orders.show', $order))->assertOk()->assertDontSee('Köszönjük a rendelésed!');
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('cart_items', 0);
        $this->get('/dashboard')->assertSee(route('orders.show', $order))->assertSee(route('orders.index'));
    }
}
