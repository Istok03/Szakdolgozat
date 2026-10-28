<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Tests\TestCase;

class OrderHistoryMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // A separate connection allows realistic schema changes outside test transactions.
        config(['database.default' => 'history_migration', 'database.connections.history_migration' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
        ]]);
        $this->artisan('migrate', ['--force' => true])->assertSuccessful();
    }

    public function test_history_migration_backfills_existing_names_and_preserves_rows(): void
    {
        $migration = require database_path('migrations/2026_10_27_100000_preserve_order_history.php');
        $migration->down();
        $order = Order::factory()->create();
        $product = Product::factory()->create(['name' => 'Régi rendelés terméke']);
        $item = $order->items()->create(['product_id' => $product->id, 'price' => 1234, 'quantity' => 2]);
        $migration->up();
        $this->assertSame('Régi rendelés terméke', $item->fresh()->product_name);
        $product->delete();
        $order->user->delete();
        $this->assertNull($item->fresh()->product_id);
        $this->assertNull($order->fresh()->user_id);
        $this->assertEquals(1234, $item->fresh()->price);
        $order->delete();
    }

}
