<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    private string $imageDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageDirectory = storage_path('framework/testing/seeder-'.Str::uuid());
        $this->app->usePublicPath($this->imageDirectory);
    }

    protected function tearDown(): void
    {
        if (isset($this->imageDirectory) && is_dir($this->imageDirectory)
            && str_starts_with(realpath($this->imageDirectory), realpath(storage_path('framework/testing')).DIRECTORY_SEPARATOR)) {
            File::deleteDirectory($this->imageDirectory);
        }
        parent::tearDown();
    }

    public function test_demo_contains_two_accounts_and_five_categories_with_five_products_each(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 2);
        $this->assertSame(1, User::where('is_admin', true)->count());
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('products', 25);
        $this->assertDatabaseCount('orders', 0);
        foreach (Category::all() as $category) {
            $this->assertSame(5, Product::where('category_id', $category->id)->count());
        }
        foreach (Product::all() as $product) {
            $this->assertGreaterThan(0, $product->stock);
            $this->assertGreaterThan(0, $product->price);
            $this->assertNotEmpty($product->description);
            $this->assertFileExists(public_path($product->image));
            $this->assertStringEndsWith('.png', $product->image);
            $this->assertNotFalse(getimagesize(public_path($product->image)));
        }
        $this->assertSame(25, Product::distinct()->count('image'));
        foreach (['admin@example.com' => 'admin123', 'vasarlo@example.com' => 'vasarlo123'] as $email => $password) {
            $this->post('/login', compact('email', 'password'))->assertSessionHasNoErrors();
            $this->assertAuthenticatedAs(User::where('email', $email)->firstOrFail());
            $this->get('/admin')->assertStatus($email === 'admin@example.com' ? 200 : 403);
            $this->post('/logout');
        }
    }

    public function test_reseeding_preserves_changes_and_existing_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $product = Product::firstOrFail();
        $product->update(['stock' => 3, 'price' => 12345]);
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $admin->password = Hash::make('ChangedPassword123!');
        $admin->save();
        $other = Product::factory()->create(['name' => 'Meglévő saját termék']);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('products', 26);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertEquals(12345, $product->fresh()->price);
        $this->assertTrue(Hash::check('ChangedPassword123!', $admin->fresh()->password));
        $this->assertNotNull($other->fresh());
    }

    public function test_legacy_svg_product_is_updated_without_duplication_or_stock_reset(): void
    {
        $category = Category::create(['name' => 'Laptopok']);
        $product = Product::factory()->create(['name' => 'Bemutató Office laptop', 'category_id' => $category->id, 'stock' => 3, 'price' => 12345]);
        $oldImage = 'images/products/demo-product-'.$product->id.'.svg';
        File::ensureDirectoryExists(public_path('images/products'));
        File::put(public_path($oldImage), '<svg xmlns="http://www.w3.org/2000/svg"/>');
        $product->update(['image' => $oldImage]);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('products', 25);
        $this->assertSame('ASUS Vivobook', $product->fresh()->name);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertEquals(12345, $product->fresh()->price);
        $this->assertFileDoesNotExist(public_path($oldImage));
        $this->assertFileExists(public_path($product->fresh()->image));
    }
}
