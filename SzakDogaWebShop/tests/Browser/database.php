<?php

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$expected = storage_path('framework/cypress/database.sqlite');
if (!$app->environment('testing') || config('database.default') !== 'sqlite'
    || str_replace('\\', '/', config('database.connections.sqlite.database')) !== str_replace('\\', '/', $expected)
    || config('database.connections.sqlite.url')) {
    throw new RuntimeException('Cypress may only access its dedicated SQLite database.');
}

if (($argv[1] ?? '') === 'reset') {
    if (!is_file($expected)) touch($expected);
    Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
    $user = App\Models\User::factory()->create([
        'name' => 'Cypress Vásárló', 'email' => 'cypress@example.test',
        'password' => Illuminate\Support\Facades\Hash::make('Cypress-Test-123!'),
    ]);
    $category = App\Models\Category::create(['name' => 'UI teszt']);
    $product = App\Models\Product::factory()->create([
        'name' => 'Cypress teszt laptop', 'price' => 10000, 'discount' => 0,
        'stock' => 5, 'category_id' => $category->id,
    ]);
    App\Models\CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);
    echo json_encode(['productId' => $product->id]);
} elseif (($argv[1] ?? '') === 'state') {
    echo json_encode([
        'orders' => App\Models\Order::all()->toArray(),
        'stock' => App\Models\Product::firstOrFail()->stock,
        'cartQuantity' => (int) App\Models\CartItem::sum('quantity'),
    ]);
} else {
    throw new RuntimeException('Unknown Cypress fixture command');
}
