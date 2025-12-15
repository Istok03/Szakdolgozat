<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 1000, 50000),
            'discount' => $this->faker->optional()->numberBetween(5, 50),
            'description' => $this->faker->sentence(10),
            'image' => 'images/products/default.png', 
        ];
    }
}
