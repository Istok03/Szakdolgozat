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
        Product::updateOrCreate(
            ['name' => 'Asus Gaming Laptop'],
           [ 'image'=> 'images/products/laptop.png',
            'price'=>'399000',
            'discount' => 25,
            'description' => 'Érős gamer laptop RTX 3050-el',
        ]);

        Product::updateOrCreate(
            ['name' => 'Wireless SteelSeries egér'],
            ['image'=> 'images/products/SSegér.png',
            'price'=>'20000',
            'discount' => 20,
            'description' => 'Wireless steel series egér',
        ]);

          Product::updateOrCreate(
            ['name' => 'JBL wireless headset'],
            [
            'image'=> 'images/products/JBLHS.png',
            'price'=>'25000',
            'discount' => 15,
            'description' => 'Wireless JBL headset',
          
        ]);

            Product::updateOrCreate(
            ['name' => 'HyperX headset'],
           [ 'image'=> 'images/products/JBLHS.png',
            'price'=>'45000',
            'discount' => 15,
            'description' => 'Wireless HyperX headset',
          
        ]);

             Product::updateOrCreate(
           [ 'name' => 'PRO X SUPERLIGHT 2 SE '],
           [ 'image'=> 'images/products/PRO X SUPERLIGHT 2 SE.png',
            'price'=>'55000',
            'discount' => 0,
            'description' => 'Wireless Logitech egér',
          
        ]);

            Product::updateOrCreate(
           [ 'name' => 'Random'],
           [ 'image'=> 'images/products/JBLHS.png',
            'price'=>'25000',
            'discount' => 0,
            'description' => 'Wireless xd headset',
          
        ]);

    }
}
