<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\CategoryOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    private function customerData(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Teszt Vásárló',
            'email' => 'customer@example.com',
            'phone' => '+36301234567',
            'address' => '3300 Eger, Teszt utca 1.',
            'payment_method' => 'cod',
        ], $overrides);
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        return $admin;
    }

    private function cartItem(User $user, Product $product, int $quantity = 1): CartItem
    {
        return CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => $quantity]);
    }

    public function test_every_admin_route_requires_authentication_and_admin_role(): void
    {
        $count = 0;
        $names = [];
        foreach (Route::getRoutes() as $route) {
            if (str_starts_with($route->uri(), 'admin')) {
                $this->assertContains('auth', $route->gatherMiddleware());
                $this->assertContains('admin', $route->gatherMiddleware());
                $count++;
            }
            if ($route->getName()) {
                $this->assertNotContains($route->getName(), $names);
                $names[] = $route->getName();
            }
        }
        $this->assertGreaterThan(10, $count);
        $this->get('/users')->assertNotFound();
        $this->post('/users')->assertNotFound();
    }

    public function test_guests_and_customers_cannot_manage_admin_resources(): void
    {
        $target = User::factory()->create();
        $order = Order::factory()->create();
        $this->get('/admin/products/create')->assertRedirect('/login');
        $this->delete('/admin/users/'.$target->id)->assertRedirect('/login');
        $this->patch('/admin/orders/'.$order->id.'/status', ['status' => 'paid'])->assertRedirect('/login');

        $this->actingAs(User::factory()->create());
        $this->get('/admin/products/create')->assertForbidden();
        $this->delete('/admin/users/'.$target->id)->assertForbidden();
        $this->patch('/admin/orders/'.$order->id.'/status', ['status' => 'paid'])->assertForbidden();
        $this->assertModelExists($target);
    }

    public function test_admin_can_change_roles_but_registration_cannot(): void
    {
        $target = User::factory()->create();
        $this->actingAs($this->admin())->put('/admin/users/'.$target->id, [
            'name' => $target->name, 'email' => $target->email, 'is_admin' => 1,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.users.index'));
        $this->assertEquals(1, $target->fresh()->is_admin);

        $this->post('/logout');
        $this->post('/register', [
            'name' => 'Customer', 'email' => 'new@example.com',
            'password' => 'password', 'password_confirmation' => 'password', 'is_admin' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertEquals(0, User::where('email', 'new@example.com')->firstOrFail()->is_admin);
    }

    public function test_admin_cannot_demote_self_or_duplicate_an_email(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $this->actingAs($admin)->put('/admin/users/'.$admin->id, [
            'name' => $admin->name, 'email' => $admin->email, 'is_admin' => 0,
        ])->assertSessionHas('error');
        $this->assertEquals(1, $admin->fresh()->is_admin);
        $this->put('/admin/users/'.$target->id, [
            'name' => $target->name, 'email' => $admin->email, 'is_admin' => 0,
        ])->assertSessionHasErrors('email');
    }

    public function test_product_details_load_category_options_and_reviews(): void
    {
        $category = Category::create(['name' => 'Laptop']);
        CategoryOption::create(['category_id' => $category->id, 'name' => 'RAM', 'price' => 1000]);
        $product = Product::factory()->create(['category_id' => $category->id]);
        $product->reviews()->create(['user_id' => User::factory()->create()->id, 'rating' => 5, 'comment' => 'Kiváló termék']);
        $this->get('/product/'.$product->id)->assertOk()->assertSee('Kiváló termék');
        $this->assertCount(1, $product->category->options);
        $this->get('/product/999999')->assertNotFound();
    }

    public function test_login_merges_existing_and_new_guest_cart_items(): void
    {
        $user = User::factory()->create();
        $first = Product::factory()->create();
        $second = Product::factory()->create();
        $this->cartItem($user, $first, 2);
        $this->withSession(['cart' => [
            $first->id => ['quantity' => 3], $second->id => ['quantity' => 4], 999999 => ['quantity' => 1],
        ]])->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasNoErrors()->assertSessionMissing('cart');
        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id, 'product_id' => $first->id, 'quantity' => 5]);
        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id, 'product_id' => $second->id, 'quantity' => 4]);
        $this->assertDatabaseCount('cart_items', 2);
    }

    public function test_registration_preserves_guest_cart(): void
    {
        $product = Product::factory()->create();
        $this->withSession(['cart' => [$product->id => ['quantity' => 2]]])->post('/register', [
            'name' => 'Customer', 'email' => 'new@example.com',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertSessionHasNoErrors()->assertSessionMissing('cart');
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_cart_actions_are_scoped_to_current_customer(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'discount' => 10]);
        $otherItem = $this->cartItem($other, $product, 5);
        $this->actingAs($user)->post('/cart/add/'.$product->id)->assertOk()->assertJsonPath('total', 900);
        $this->post('/cart/increase/'.$product->id)->assertJsonPath('total', 1800);
        $this->post('/cart/decrease/'.$product->id)->assertJsonPath('total', 900);
        $this->post('/cart/decrease/'.$product->id)->assertJsonPath('total', 900);
        $this->post('/cart/remove/'.$product->id)->assertJsonPath('total', 0);
        $this->assertEquals(5, $otherItem->fresh()->quantity);
    }

    public function test_guest_cart_can_be_updated_and_emptied(): void
    {
        $product = Product::factory()->create(['price' => 1000, 'discount' => 10]);
        $this->post('/cart/add/'.$product->id)->assertJsonPath('total', 900);
        $this->post('/cart/increase/'.$product->id)->assertJsonPath('total', 1800);
        $this->get('/cart')->assertOk();
        $this->post('/cart/remove/'.$product->id)->assertJsonPath('total', 0);
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_invalid_checkout_preserves_cart_and_creates_no_order(): void
    {
        $user = User::factory()->create();
        $item = $this->cartItem($user, Product::factory()->create(), 2);
        $this->actingAs($user)->from('/checkout')->post('/order/store', $this->customerData(['payment_method' => 'card']))
            ->assertRedirect('/checkout')->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertModelExists($item);
    }

    public function test_checkout_saves_current_prices_payment_and_all_items_only_once(): void
    {
        $user = User::factory()->create();
        $first = Product::factory()->create(['price' => 1000, 'discount' => 10]);
        $second = Product::factory()->create(['price' => 2500, 'discount' => 0]);
        $this->cartItem($user, $first, 2);
        $this->cartItem($user, $second);
        $otherItem = $this->cartItem(User::factory()->create(), $first);

        $this->actingAs($user)->get('/checkout')->assertOk()->assertSee('Utánvét')->assertDontSee('card_number');
        $this->post('/order/store', $this->customerData(['total' => 1, 'user_id' => $otherItem->user_id]))
            ->assertRedirect(route('orders.confirmation', Order::firstOrFail()))->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(4300, $order->total);
        $this->assertSame('cod', $order->payment_method);
        $this->assertCount(2, $order->items);
        $this->assertEquals(900, $order->items->firstWhere('product_id', $first->id)->price);
        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id]);
        $this->assertModelExists($otherItem);
        $this->post('/order/store', $this->customerData())->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_order_write_failure_rolls_back_and_keeps_cart(): void
    {
        $user = User::factory()->create();
        $item = $this->cartItem($user, Product::factory()->create());
        OrderItem::creating(fn () => throw new \RuntimeException('Simulated database failure'));
        $this->withoutExceptionHandling();
        try {
            $this->actingAs($user)->post('/order/store', $this->customerData());
            $this->fail('Expected the simulated failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        } finally {
            OrderItem::flushEventListeners();
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertModelExists($item);
    }

    public function test_admin_order_edit_saves_customer_fields_and_supported_statuses(): void
    {
        $order = Order::factory()->create();
        $this->actingAs($this->admin());
        foreach (array_keys(Order::STATUSES) as $status) {
            $this->put('/admin/orders/'.$order->id, $this->customerData(['status' => $status]))
                ->assertSessionHasNoErrors()->assertRedirect(route('admin.orders.index'));
            $this->assertSame($status, $order->fresh()->status);
        }
        $this->assertSame('Teszt Vásárló', $order->fresh()->name);
        $this->patch('/admin/orders/'.$order->id.'/status', ['status' => 'processing'])->assertSessionHasNoErrors();
        $this->assertSame('processing', $order->fresh()->status);
        $this->patch('/admin/orders/'.$order->id.'/status', ['status' => 'invalid'])->assertSessionHasErrors('status');
    }

    public function test_admin_order_details_display_every_product(): void
    {
        $order = Order::factory()->create();
        foreach (['Teszt Laptop', 'Teszt Billentyűzet'] as $name) {
            $order->items()->create(['product_id' => Product::factory()->create(['name' => $name])->id, 'quantity' => 1, 'price' => 1000]);
        }
        $this->actingAs($this->admin())->get('/admin/orders/'.$order->id)
            ->assertOk()->assertSee('Teszt Laptop')->assertSee('Teszt Billentyűzet');
    }

    public function test_profile_and_dashboard_render_component_content(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/profile')->assertOk()->assertSee('Jelszó módosítása');
        $this->get('/dashboard')->assertOk()->assertSee('Fiókom áttekintése');
    }
}
