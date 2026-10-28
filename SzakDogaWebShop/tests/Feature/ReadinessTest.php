<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_role_form_uses_the_controller_field_and_selects_current_role(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->get(route('admin.users.edit', $admin));
        $response->assertOk()->assertSee('name="is_admin"', false)->assertDontSee('name="role"', false);
        $this->assertMatchesRegularExpression('/value="1"\s+selected/', $response->getContent());
        $this->get(route('admin.users.index'))->assertSee('Admin');
    }

    public function test_admin_lists_have_working_pagination(): void
    {
        User::factory()->count(11)->create();
        Order::factory()->count(11)->create();
        $this->actingAs($this->admin());
        foreach (['users', 'orders'] as $resource) {
            $this->get('/admin/'.$resource)->assertOk()->assertSee('page=2', false)->assertSee('Következő');
            $this->get('/admin/'.$resource.'?page=2')->assertOk()->assertSee('Előző');
        }
    }

    public function test_checkout_snapshot_and_order_survive_product_and_account_deletion(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Eredeti laptop', 'price' => 10000, 'discount' => 20, 'image' => 'images/products/history-test-missing.png']);
        CartItem::create(['user_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 2]);
        $this->actingAs($customer)->post(route('order.store'), [
            'name' => 'Teszt Vásárló', 'email' => $customer->email, 'phone' => '12345678',
            'address' => 'Eger, Teszt utca 1.', 'payment_method' => 'cod',
        ])->assertSessionHasNoErrors();
        $order = Order::firstOrFail();
        $item = $order->items()->firstOrFail();
        $product->update(['name' => 'Átnevezett laptop', 'price' => 20000]);
        $this->assertSame('Eredeti laptop', $item->fresh()->product_name);

        $this->actingAs($this->admin())->delete(route('admin.products.destroy', $product))->assertRedirect();
        $this->delete(route('admin.users.destroy', $customer))->assertRedirect();
        $this->assertNull($order->fresh()->user_id);
        $this->assertNull($item->fresh()->product_id);
        $this->assertEquals(16000, $order->fresh()->total);
        $this->assertEquals(8000, $item->fresh()->price);
        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Eredeti laptop')->assertSee('Teszt Vásárló');
    }

    public function test_contact_messages_are_validated_saved_and_visible_only_to_admins(): void
    {
        $this->from('/contact')->post('/contact', ['name' => 'Teszt', 'email' => 'invalid', 'message' => ''])
            ->assertRedirect('/contact')->assertSessionHasErrors(['email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
        $data = ['name' => 'Teszt Feladó', 'email' => 'sender@example.com', 'message' => '<script>alert(1)</script> Kérdésem van.'];
        $this->post('/contact', $data)->assertRedirect('/contact')->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', $data);
        $this->get('/admin/messages')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin/messages')->assertForbidden();
        $this->actingAs($this->admin())->get('/admin/messages')->assertOk()
            ->assertSee($data['message'])->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_contact_submission_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', ['name' => 'Teszt', 'email' => 'sender@example.com', 'message' => 'Kérdés'])
                ->assertRedirect('/contact');
        }
        $this->post('/contact', ['name' => 'Teszt', 'email' => 'sender@example.com', 'message' => 'Kérdés'])->assertStatus(429);
        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_catalog_filters_discounted_price_category_and_name_and_keeps_query_on_pages(): void
    {
        $category = Category::create(['name' => 'Laptopok']);
        Product::factory()->count(13)->create(['name' => 'Szűrt laptop', 'category_id' => $category->id, 'price' => 10000, 'discount' => 20]);
        Product::factory()->create(['name' => 'Drága laptop', 'category_id' => $category->id, 'price' => 12000, 'discount' => 0]);
        Product::factory()->create(['name' => 'Más kategória', 'category_id' => null, 'price' => 8000, 'discount' => 0]);
        $query = ['name' => 'laptop', 'category' => $category->id, 'min' => 8000, 'max' => 8000, 'sale' => 1];
        $response = $this->get(route('products', $query));
        $response->assertOk()->assertSee('Szűrt laptop')->assertDontSee('Drága laptop')->assertDontSee('Más kategória')
            ->assertSee('name="category" value="'.$category->id.'"', false)->assertSee('value="8000"', false);
        $this->assertEquals(13, $response->viewData('products')->total());
        $next = $response->viewData('products')->nextPageUrl();
        parse_str(parse_url($next, PHP_URL_QUERY), $params);
        $this->assertEquals($query + ['page' => 2], $params);
        $this->get($next)->assertOk()->assertSee('Szűrt laptop');
    }

    public function test_catalog_handles_zero_empty_invalid_filters_and_offer_scope(): void
    {
        Product::factory()->create(['name' => 'Ajándék', 'price' => 1000, 'discount' => 100]);
        Product::factory()->create(['name' => 'Normál termék', 'price' => 1000, 'discount' => 0]);
        $this->get('/products?max=0')->assertOk()->assertSee('Ajándék')->assertDontSee('Normál termék');
        $this->get('/products?min=9999999')->assertOk()->assertSee('Nincs a szűrésnek megfelelő termék.');
        $this->from('/products')->get('/products?min=20&max=10')->assertSessionHasErrors('max');
        $this->from('/products')->get('/products?min=-1')->assertSessionHasErrors('min');
        $this->get('/offers?sale=0')->assertOk()->assertSee('Ajándék')->assertDontSee('Normál termék');
    }

    public function test_dashboard_shows_only_current_customers_summary(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $customer = User::factory()->create();
        $this->actingAs($customer)->get('/dashboard')->assertOk()->assertSee('Még nem adtál le rendelést.');
        $own = Order::factory()->create(['user_id' => $customer->id, 'total' => 1234]);
        Order::factory()->create(['total' => 999999]);
        $response = $this->get('/dashboard')->assertOk()->assertSee('1 234 Ft')->assertDontSee('999 999 Ft');
        $this->assertEquals(1, $response->viewData('orderCount'));
        $this->assertTrue($own->is($response->viewData('latestOrder')));
    }
}
