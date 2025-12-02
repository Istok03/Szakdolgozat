<?php

namespace Database\Seeders;

use GuzzleHttp\Promise\Create;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Asus Gaming Laptop',
            'description' => 'Érős gamer laptop RTX 3050-el',
            'price'=>'399000',
            'discount' => 25,
            'image'=> 'images/products/laptop.png',
        ]);

        Product::create([
            'name' => 'Wireless SteelSeries egér',
            'description' => 'Wireless steel series egér',
            'price'=>'20000',
            'discount' => 20,
            'image'=> 'images/products/SSegér.png',
        ]);

          Product::create([
            'name' => 'JBL wireless headset',
            'description' => 'Wireless JBL headset',
            'price'=>'25000',
            'discount' => 15,
            'image'=> 'images/products/JBLHS.png',
        ]);

    }
}
