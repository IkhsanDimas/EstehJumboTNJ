<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreamyOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database with Es Teh Jumbo categories and products
        $this->seed(\Database\Seeders\EsTehJumboSeeder::class);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Es Teh Jumbo');
        $response->assertSee("Teh Jumbo");
    }

    public function test_menu_catalog_loads_successfully(): void
    {
        $response = $this->get('/menu');
        $response->assertStatus(200);
        $response->assertSee('Semua Menu');
    }

    public function test_can_add_product_to_cart_and_view_cart(): void
    {
        $product = Product::first();

        // 1. Add to cart
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 2. View cart page
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Total Tagihan');
    }

    public function test_can_update_cart_quantity(): void
    {
        $product = Product::first();

        // Add to cart
        $this->post('/cart/add', ['product_id' => $product->id]);

        // Increase quantity
        $response = $this->post('/cart/update', [
            'product_id' => $product->id,
            'action' => 'increase',
        ]);
        $response->assertRedirect();

        // Verify quantity in session is 2
        $cartItem = collect(session('cart'))->firstWhere('product_id', $product->id);
        $this->assertNotNull($cartItem);
        $this->assertEquals(2, $cartItem['quantity']);

        // Decrease quantity
        $response = $this->post('/cart/update', [
            'product_id' => $product->id,
            'action' => 'decrease',
        ]);
        $response->assertRedirect();

        // Verify quantity in session is 1
        $cartItem = collect(session('cart'))->firstWhere('product_id', $product->id);
        $this->assertNotNull($cartItem);
        $this->assertEquals(1, $cartItem['quantity']);
    }

    public function test_can_remove_product_from_cart(): void
    {
        $product = Product::first();

        // Add to cart
        $this->post('/cart/add', ['product_id' => $product->id]);

        // Remove from cart
        $response = $this->post('/cart/remove', [
            'product_id' => $product->id,
        ]);
        $response->assertRedirect();

        // Verify cart is empty in session
        $cart = session('cart');
        $this->assertEmpty($cart);
    }

    public function test_can_checkout_successfully(): void
    {
        $product = Product::first();

        // Add to cart
        $this->post('/cart/add', ['product_id' => $product->id]);

        // Submit checkout
        $response = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'phone' => '081234567890',
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'now',
            'notes' => 'Tolong kirim cepat',
        ]);

        // It should redirect to success screen
        $response->assertRedirect();
        $this->assertTrue(session()->has('whatsapp_url'));

        // Follow redirect or fetch success page
        $order = \App\Models\Order::first();
        $this->assertNotNull($order);
        $this->assertStringContainsString('Tolong kirim cepat', $order->notes);

        $successResponse = $this->get(route('checkout.success', $order));
        $successResponse->assertStatus(200);
        $successResponse->assertSee($order->order_number);
        $successResponse->assertSee('Pesanan Berhasil');
    }

    public function test_checkout_fails_when_store_is_closed(): void
    {
        // 1. Force the store to be closed
        $store = \App\Models\StoreSetting::current();
        $store->is_open = false;
        $store->save();

        $product = Product::first();

        // 2. Add to cart
        $this->post('/cart/add', ['product_id' => $product->id]);

        // 3. Try to access checkout page
        $response = $this->get('/checkout');
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Maaf, kedai kami saat ini sedang tutup. Silakan kembali lagi nanti.');

        // 4. Try to submit checkout
        $response = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'phone' => '081234567890',
            'delivery_type' => 'pickup',
            'payment_method' => 'cash',
            'schedule_type' => 'now',
            'notes' => 'Tolong kirim cepat',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Maaf, kedai kami saat ini sedang tutup. Transaksi tidak dapat diproses.');

        // Verify order is not created
        $this->assertEquals(0, \App\Models\Order::count());
    }

    public function test_checkout_success_prevents_idor(): void
    {
        // 1. Create two orders
        $order1 = \App\Models\Order::create([
            'order_number' => 'ETJ-20260612-ABCDEF',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => 10000,
            'grand_total' => 10000,
            'payment_method' => 'cash',
        ]);

        $order2 = \App\Models\Order::create([
            'order_number' => 'ETJ-20260612-GHIJKL',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => 15000,
            'grand_total' => 15000,
            'payment_method' => 'cash',
        ]);

        // 2. Put order 1 in session as last_order_id
        session(['last_order_id' => $order1->id]);

        // 3. User should be able to view success page of order 1
        $response = $this->get(route('checkout.success', $order1));
        $response->assertStatus(200);

        // 4. User should NOT be able to view success page of order 2 (IDOR protection)
        $response2 = $this->get(route('checkout.success', $order2));
        $response2->assertStatus(403);
    }

    public function test_stock_deduction_on_order_completion(): void
    {
        // 1. Setup products and stock
        $product = Product::first();
        $ingredient = \App\Models\Ingredient::create([
            'name' => 'Bahan Uji',
            'unit' => 'gram',
            'current_stock' => 100.0,
            'min_stock' => 10.0,
        ]);
        
        \App\Models\Recipe::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredient->id,
            'quantity_per_unit' => 2.5,
        ]);

        // Create user with owner role to complete order
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('owner');

        // Create pending order
        $order = \App\Models\Order::create([
            'order_number' => 'ETJ-20260612-OUT123',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => $product->price,
            'grand_total' => $product->price,
            'payment_method' => 'cash',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => 4,
            'line_total' => $product->price * 4,
        ]);

        // 2. Complete order as admin owner
        $this->actingAs($admin)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'paid',
            ]);
            
        $this->actingAs($admin)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'preparing',
            ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'ready',
            ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.orders.update-status', $order), [
                'status' => 'completed',
            ]);

        $response->assertRedirect();
        
        // 3. Verify stock has been deducted
        // Initial stock 100.0, order quantity 4, recipe quantity per unit 2.5. Total used = 10.0
        // New stock should be 90.0
        $this->assertEquals(90.0, $ingredient->fresh()->current_stock);

        // Verify stock movement record is created
        $movement = \App\Models\StockMovement::where('ingredient_id', $ingredient->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('out', $movement->type);
        $this->assertEquals(10.0, $movement->quantity);
    }

    public function test_admin_can_replenish_ingredient_stock(): void
    {
        // 1. Setup admin and ingredient
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('owner');

        $ingredient = \App\Models\Ingredient::create([
            'name' => 'Daun Teh Premium',
            'unit' => 'gram',
            'current_stock' => 1000.0,
            'min_stock' => 100.0,
            'cost_per_unit' => 15.0,
        ]);

        // 2. Post replenishment as admin
        $response = $this->actingAs($admin)
            ->post(route('admin.ingredients.replenish', $ingredient), [
                'quantity' => 500.0,
            ]);

        $response->assertRedirect();
        
        // 3. Verify stock increased
        $this->assertEquals(1500.0, $ingredient->fresh()->current_stock);

        // 4. Verify StockMovement created
        $movement = \App\Models\StockMovement::where('ingredient_id', $ingredient->id)
            ->where('type', 'in')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(500.0, $movement->quantity);
        $this->assertEquals('Penambahan stok oleh admin', $movement->reason);
    }

    public function test_admin_can_update_ingredient_details(): void
    {
        // 1. Setup admin and ingredient
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('owner');

        $ingredient = \App\Models\Ingredient::create([
            'name' => 'Gula Pasir Premium',
            'unit' => 'gram',
            'current_stock' => 2000.0,
            'min_stock' => 200.0,
            'cost_per_unit' => 18.0,
        ]);

        // 2. Post updates as admin
        $response = $this->actingAs($admin)
            ->post(route('admin.ingredients.update', $ingredient), [
                'min_stock' => 500.0,
                'cost_per_unit' => 20.0,
            ]);

        $response->assertRedirect();

        // 3. Verify changes persisted
        $fresh = $ingredient->fresh();
        $this->assertEquals(500.0, $fresh->min_stock);
        $this->assertEquals(20.0, $fresh->cost_per_unit);
    }

    public function test_admin_cannot_delete_product_with_order_items(): void
    {
        // 1. Setup admin and product
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('owner');

        $product = Product::first();

        // 2. Create an order and order item for this product
        $order = \App\Models\Order::create([
            'order_number' => 'ETJ-20260612-TST999',
            'type' => 'online_pickup',
            'status' => 'pending',
            'subtotal' => $product->price,
            'grand_total' => $product->price,
            'payment_method' => 'cash',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => 1,
            'line_total' => $product->price,
        ]);

        // 3. Try to delete product as admin
        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        // 4. Verify it redirects back with error message and product is NOT deleted
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error', 'Gagal menghapus menu minuman. Menu ini masih terikat dengan transaksi/pesanan pelanggan.');
        
        $this->assertNotNull(Product::find($product->id));
    }
}
