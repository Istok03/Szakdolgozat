<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DemoPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function cart(): Product
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $product = Product::factory()->create(['price' => 10000, 'discount' => 10, 'stock' => 5]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);
        return $product;
    }

    private function data(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Teszt Vásárló', 'email' => 'demo@example.com', 'phone' => '12345678',
            'address' => 'Eger, Teszt utca 1.', 'payment_method' => 'demo_card', 'shipping_method' => 'courier',
            'card_number' => '4242424242424242', 'card_expiry' => now()->addYear()->format('m/y'), 'card_cvc' => '987',
        ], $overrides);
    }

    public static function acceptedCards(): array
    {
        return [['4242 4242 4242 4242', 'Visa', '4242'], ['5555-5555-5555-4444', 'Mastercard', '4444']];
    }

    #[DataProvider('acceptedCards')]
    public function test_success_reserves_stock_and_saves_only_safe_server_generated_metadata(string $number, string $brand, string $last): void
    {
        $product = $this->cart();
        $this->post('/order/store', $this->data([
            'card_number' => $number, 'total' => 1, 'payment_reference' => 'forged',
            'card_brand' => 'forged', 'card_last_four' => '0000', 'payment_status' => 'paid',
        ]))->assertSessionHasNoErrors();
        $order = Order::sole();
        $this->assertSame('demo_approved', $order->payment_status);
        $this->assertSame('pending', $order->status);
        $this->assertSame($brand, $order->card_brand);
        $this->assertSame($last, $order->card_last_four);
        $this->assertStringStartsWith('DEMO-', $order->payment_reference);
        $this->assertEquals(18000 + config('shipping.methods.courier.fee'), $order->total);
        $this->assertEquals(3, $product->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);
        foreach (['card_number', 'card_expiry', 'card_cvc'] as $field) {
            $this->assertArrayNotHasKey($field, $order->getAttributes());
            $this->assertNull(session('_old_input.'.$field));
        }
        $this->get(route('orders.show', $order))->assertOk()->assertSee('Sikeres szimuláció')->assertSee($last)->assertDontSee(str_replace([' ', '-'], '', $number));
        $this->post('/order/store', $this->data())->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 1);
        $this->assertEquals(3, $product->fresh()->stock);
    }

    public static function rejectedCards(): array
    {
        return [
            'declined visa' => [['card_number' => '4000000000000002'], 'payment_method'],
            'declined mastercard' => [['card_number' => '5105105105105100'], 'payment_method'],
            'invalid luhn' => [['card_number' => '4242424242424241'], 'card_number'],
            'valid but not allowed' => [['card_number' => '4111111111111111'], 'card_number'],
            'unsupported brand' => [['card_number' => '6011111111111117'], 'card_number'],
            'expired' => [['card_expiry' => '12/20'], 'card_expiry'],
            'invalid month' => [['card_expiry' => '13/30'], 'card_expiry'],
            'bad cvc' => [['card_cvc' => '12'], 'card_cvc'],
            'array input' => [['card_number' => ['4242424242424242']], 'card_number'],
            'missing number' => [['card_number' => ''], 'card_number'],
        ];
    }

    #[DataProvider('rejectedCards')]
    public function test_rejection_preserves_cart_and_stock_and_does_not_flash_card_input(array $overrides, string $error): void
    {
        $product = $this->cart();
        $this->from('/checkout')->post('/order/store', $this->data($overrides))
            ->assertRedirect('/checkout')->assertSessionHasErrors($error);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertEquals(5, $product->fresh()->stock);
        foreach (['card_number', 'card_expiry', 'card_cvc'] as $field) {
            $this->assertArrayNotHasKey($field, session('_old_input', []));
        }
        $this->assertSame('demo_card', session('_old_input.payment_method'));
        $this->get('/checkout')->assertOk()->assertSee('Tesztkártya adatai')->assertDontSee('value="987"', false);
    }

    public function test_unrelated_validation_and_stock_errors_do_not_flash_sensitive_input(): void
    {
        $product = $this->cart();
        $this->post('/order/store', $this->data(['email' => 'invalid']))->assertSessionHasErrors('email');
        $this->assertArrayNotHasKey('card_number', session('_old_input', []));
        $this->assertArrayNotHasKey('card_cvc', session('_old_input', []));
        $product->update(['stock' => 0]);
        $this->post('/order/store', $this->data())->assertSessionHasErrors('cart');
        $this->assertArrayNotHasKey('card_number', session('_old_input', []));
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_current_month_is_valid_and_cod_does_not_require_card_data(): void
    {
        $this->cart();
        $this->post('/order/store', $this->data(['card_expiry' => now()->format('m/y')]))->assertSessionHasNoErrors();
        $this->cart();
        $data = $this->data(['payment_method' => 'cod']);
        unset($data['card_number'], $data['card_expiry'], $data['card_cvc']);
        $this->post('/order/store', $data)->assertSessionHasNoErrors();
        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->payment_reference);
        $this->assertNull($order->card_last_four);
    }

    public function test_json_validation_does_not_echo_card_data(): void
    {
        $this->cart();
        $response = $this->postJson('/order/store', $this->data(['card_number' => '4111111111111111']));
        $response->assertUnprocessable()->assertJsonValidationErrors('card_number')->assertDontSee('4111111111111111');
    }
}
