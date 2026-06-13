<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed(\Database\Seeders\EsTehJumboSeeder::class);

        // Fetch owner and category
        $this->owner = User::where('email', 'owner@esthejumbo.test')->first();
        $this->category = Category::first();
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $response = $this->get(route('admin.products.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_products_index(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Menu Minuman');
        $response->assertSee('Teh Jumbo'); // seeded product
    }

    public function test_admin_can_view_create_product_page(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Menu Baru');
    }

    public function test_admin_can_store_product_without_image(): void
    {
        $response = $this->actingAs($this->owner)
            ->post(route('admin.products.store'), [
                'name' => 'Es Teh Lychee Segar',
                'category_id' => $this->category->id,
                'price' => 8500,
                'description' => 'Es teh dengan buah leci segar asli',
                'is_available' => 1,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Es Teh Lychee Segar',
            'price' => 8500,
            'image_path' => 'images/es-teh-original', // default fallback path if no image uploaded
        ]);
    }

    public function test_admin_can_store_product_with_image(): void
    {
        $file = UploadedFile::fake()->image('custom_tea.png', 100, 100);

        $response = $this->actingAs($this->owner)
            ->post(route('admin.products.store'), [
                'name' => 'Es Teh Lychee Kustom',
                'category_id' => $this->category->id,
                'price' => 9000,
                'description' => 'Kustom tea',
                'is_available' => 1,
                'image' => $file,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $product = Product::where('name', 'Es Teh Lychee Kustom')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('images/products/product_', $product->image_path);
        
        // Assert file exists in public directory on disk
        $this->assertTrue(file_exists(public_path($product->image_path)));

        // Cleanup physical file uploaded during testing
        if (file_exists(public_path($product->image_path))) {
            @unlink(public_path($product->image_path));
        }
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::first();

        $response = $this->actingAs($this->owner)
            ->put(route('admin.products.update', $product), [
                'name' => 'Teh Jumbo Gede Banget',
                'category_id' => $product->category_id,
                'price' => 6000,
                'description' => 'Porsi super jumbo',
                'is_available' => 0,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Teh Jumbo Gede Banget',
            'price' => 6000,
            'is_available' => 0,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::first();

        $response = $this->actingAs($this->owner)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_view_dashboard_with_analytics(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'totalSales',
            'totalOrdersCount',
            'teaStock',
            'teaMinStock',
            'recentOrders',
            'salesLabels',
            'salesValues',
            'topLabels',
            'topValues'
        ]);
        $response->assertSee('Tren Penjualan');
        $response->assertSee('Varian Minuman Terlaris');
    }

    public function test_admin_can_update_order_status_sequence(): void
    {
        $order = \App\Models\Order::create([
            'order_number' => 'ETJ-TEST-1234',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => 15000,
            'ongkir' => 0,
            'grand_total' => 15000,
            'payment_method' => 'cash',
        ]);

        // Sequence: pending -> paid -> preparing -> ready -> completed
        
        // 1. pending -> paid
        $response = $this->actingAs($this->owner)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'paid'
            ]);
        $response->assertRedirect();
        $this->assertEquals('paid', $order->fresh()->status);

        // 2. paid -> preparing
        $response = $this->actingAs($this->owner)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'preparing'
            ]);
        $response->assertRedirect();
        $this->assertEquals('preparing', $order->fresh()->status);

        // 3. preparing -> ready
        $response = $this->actingAs($this->owner)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'ready'
            ]);
        $response->assertRedirect();
        $this->assertEquals('ready', $order->fresh()->status);

        // 4. ready -> completed
        $response = $this->actingAs($this->owner)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'completed'
            ]);
        $response->assertRedirect();
        $this->assertEquals('completed', $order->fresh()->status);
    }

    public function test_admin_cannot_transition_to_invalid_status(): void
    {
        $order = \App\Models\Order::create([
            'order_number' => 'ETJ-TEST-5678',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => 15000,
            'ongkir' => 0,
            'grand_total' => 15000,
            'payment_method' => 'cash',
        ]);

        // Invalid: pending -> completed directly (not allowed in TRANSITIONS)
        $response = $this->actingAs($this->owner)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'completed'
            ]);
            
        $response->assertSessionHasErrors('status');
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_get_latest_order_id(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('admin.orders.latest-id'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'latest_id',
            'count'
        ]);
    }
}
