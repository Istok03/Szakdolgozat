<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // minden rendeléshez létrehoz egy usert
            'status' => $this->faker->randomElement(['pending', 'paid', 'shipped', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}

