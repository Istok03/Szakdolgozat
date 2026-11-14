<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Demo prices and stock; photos are bundled with the project.
        $photos = [
            'Laptopok' => ['1794564475_kép_2026-11-13_110753973.png', '1794563886_kép_2026-11-13_105804129.png', '1794564068_kép_2026-11-13_110105013.png', '1794564369_kép_2026-11-13_110606266.png', '1794566397_kép_2026-11-13_113956258.png'],
            'Egerek' => ['1794564774_kép_2026-11-13_111250842.png', '1794564836_kép_2026-11-13_111355379.png', '1794565168_kép_2026-11-13_111905402.png', '1794565031_kép_2026-11-13_111710425.png', '1794566819_kép_2026-11-13_114658693.png'],
            'Billentyűzetek' => ['1794565197_kép_2026-11-13_111957134.png', '1794565462_kép_2026-11-13_112421493.png', '1794565431_kép_2026-11-13_112350387.png', 'AulaF75.png', '1794565545_kép_2026-11-13_112544369.png'],
            'Monitorok' => ['1794569222_kép_2026-11-13_122700593.png', '1794569521_kép_2026-11-13_123200742.png', '1794569541_kép_2026-11-13_123220148.png', '1794569521_kép_2026-11-13_123200742.png', '1794569541_kép_2026-11-13_123220148.png'],
            'Fejhallgatók' => ['1794567121_kép_2026-11-13_115200472.png', '1794565576_kép_2026-11-13_112615410.png', '1794565720_kép_2026-11-13_112839624.png', '1794565696_kép_2026-11-13_112814505.png', '1794567101_kép_2026-11-13_115140822.png'],
        ];
        $laptopNames = ['ASUS Vivobook', 'Apple MacBook Air', 'Apple MacBook Pro', 'ASUS ROG Strix', 'ASUS TUF Gaming'];
        foreach ($photos as $files) {
            foreach ($files as $file) {
                if (!is_file(base_path('public/images/products/'.$file))) {
                    throw new \RuntimeException('Hiányzó seeder termékkép: '.$file);
                }
            }
        }
        $catalog = [
            'Laptopok' => [
                ['Bemutató Office laptop', 159990, 'Dokumentumokhoz, böngészéshez és hétköznapi irodai feladatokhoz összeállított bemutató laptop.'],
                ['Bemutató Student laptop', 189990, 'Tanuláshoz, online órákhoz és beadandók készítéséhez használható bemutató laptop.'],
                ['Bemutató Business laptop', 259990, 'Rendszeres munkavégzéshez és több alkalmazás egyidejű használatához szánt bemutató laptop.'],
                ['Bemutató Gaming laptop', 349990, 'Játékos konfigurációt szemléltető bemutató laptop a webshop rendelési folyamatának kipróbálásához.'],
                ['Bemutató Creator laptop', 429990, 'Kreatív munkára szánt laptopot szemléltető bemutató termék.'],
            ],
            'Egerek' => [
                ['Bemutató Office egér', 3990, 'Vezetékes irodai egeret szemléltető bemutató termék.'],
                ['Bemutató Wireless egér', 6990, 'Vezeték nélküli egeret szemléltető bemutató termék otthoni használathoz.'],
                ['Bemutató Silent egér', 8990, 'Csendes működésű irodai egeret szemléltető bemutató termék.'],
                ['Bemutató Gaming egér', 14990, 'Játékosoknak szánt egeret szemléltető bemutató termék.'],
                ['Bemutató Ergo egér', 11990, 'Ergonomikus egeret szemléltető bemutató termék.'],
            ],
            'Billentyűzetek' => [
                ['Bemutató Office billentyűzet', 5990, 'Irodai szövegbevitelhez szánt billentyűzetet szemléltető bemutató termék.'],
                ['Bemutató Wireless billentyűzet', 10990, 'Vezeték nélküli billentyűzetet szemléltető bemutató termék.'],
                ['Bemutató Compact billentyűzet', 14990, 'Kompakt asztali billentyűzetet szemléltető bemutató termék.'],
                ['Bemutató Mechanical billentyűzet', 24990, 'Mechanikus billentyűzetet szemléltető bemutató termék.'],
                ['Bemutató Gaming billentyűzet', 19990, 'Játékos billentyűzetet szemléltető bemutató termék.'],
            ],
            'Monitorok' => [
                ['Bemutató Office monitor', 44990, 'Irodai monitor bemutatóadatlapja dokumentumkezeléshez és böngészéshez.'],
                ['Bemutató Home monitor', 54990, 'Otthoni használatra szánt monitor bemutatóadatlapja.'],
                ['Bemutató Gaming monitor', 79990, 'Játékos monitor bemutatóadatlapja.'],
                ['Bemutató Designer monitor', 119990, 'Kreatív munkára szánt monitor bemutatóadatlapja.'],
                ['Bemutató Ultrawide monitor', 149990, 'Széles képernyős monitor bemutatóadatlapja többablakos munkához.'],
            ],
            'Fejhallgatók' => [
                ['Bemutató Office headset', 7990, 'Online megbeszélésekhez szánt mikrofonos fejhallgatót szemléltető bemutató termék.'],
                ['Bemutató Wireless fejhallgató', 14990, 'Vezeték nélküli fejhallgatót szemléltető bemutató termék.'],
                ['Bemutató Gaming headset', 19990, 'Játékos mikrofonos fejhallgatót szemléltető bemutató termék.'],
                ['Bemutató Studio fejhallgató', 29990, 'Stúdiójellegű fejhallgatót szemléltető bemutató termék.'],
                ['Bemutató Travel fejhallgató', 24990, 'Utazáshoz szánt fejhallgatót szemléltető bemutató termék.'],
            ],
        ];
        foreach ($catalog as $categoryName => $entries) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            foreach ($entries as $index => [$name, $price, $description]) {
                $legacyName = $name;
                $name = $categoryName === 'Laptopok' ? $laptopNames[$index] : $name;
                $product = Product::where('name', $name)->first();
                if (!$product && $legacyName !== $name) {
                    $product = Product::where('name', $legacyName)->where('image', 'like', 'images/products/demo-product-%.svg')->first();
                    if ($product) $product->update(['name' => $name]);
                }
                $product ??= Product::create([
                    'name' => $name,
                    'category_id' => $category->id, 'price' => $price,
                    'discount' => $index === 0 ? 10 : 0, 'stock' => 10 + $index * 5,
                    'description' => $description.' Szakdolgozati mintaadat, nem valós kereskedelmi ajánlat.',
                ]);
                $legacyImage = 'images/products/demo-product-'.$product->id.'.svg';
                if ($product->wasRecentlyCreated || $product->image === $legacyImage) {
                    // Each product owns its image, so editing one cannot break another.
                    $image = 'images/products/demo-product-'.$product->id.'.png';
                    File::ensureDirectoryExists(public_path('images/products'));
                    if (!File::copy(base_path('public/images/products/'.$photos[$categoryName][$index]), public_path($image))) {
                        throw new \RuntimeException('A termékfotó nem másolható.');
                    }
                    $product->update(['image' => $image]);
                    if (is_file(public_path($legacyImage)) && !Product::where('image', $legacyImage)->exists()) {
                        File::delete(public_path($legacyImage));
                    }
                }
            }
        }
    }
}
