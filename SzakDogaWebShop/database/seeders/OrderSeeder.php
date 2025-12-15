<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 10 rendelés létrehozása
        Order::factory()
            ->count(10)
            ->create()
            ->each(function ($order) {
                // minden rendeléshez 2-5 rendelési tétel
                OrderItem::factory()
                    ->count(rand(2, 5))
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });
    }
}

