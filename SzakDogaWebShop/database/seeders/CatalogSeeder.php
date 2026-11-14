<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public const CATEGORIES = ['Laptopok', 'Egerek', 'Billentyűzetek', 'Monitorok', 'Fejhallgatók'];

    public function run(): void
    {
        foreach (self::CATEGORIES as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
        $this->call(ProductSeeder::class);
    }
}
