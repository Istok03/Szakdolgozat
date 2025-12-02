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
            'image'=> 'images/products/laptop.png',
            'price'=>'399000',
            'discount' => 25,
            'description' => 'Érős gamer laptop RTX 3050-el',
        ]);

        Product::create([
            'name' => 'Wireless SteelSeries egér',
            'image'=> 'images/products/SSegér.png',
            'price'=>'20000',
            'discount' => 20,
            'description' => 'Wireless steel series egér',
        ]);

          Product::create([
            'name' => 'JBL wireless headset',
            'image'=> 'images/products/JBLHS.png',
            'price'=>'25000',
            'discount' => 15,
            'description' => 'Wireless JBL headset',
          
        ]);

    }
}
