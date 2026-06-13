<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EsTehJumboSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = ['owner', 'cashier', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Users
        $owner = User::updateOrCreate(
            ['email' => 'owner@esthejumbo.test'],
            [
                'name'     => 'Cindy Tnioh',
                'phone'    => '081200000001',
                'password' => Hash::make('password'),
            ],
        );
        $owner->syncRoles(['owner']);

        $cashier = User::updateOrCreate(
            ['email' => 'kasir@esthejumbo.test'],
            [
                'name'     => 'Kasir Satu',
                'phone'    => '081200000002',
                'password' => Hash::make('password'),
            ],
        );
        $cashier->syncRoles(['cashier']);

        // 3. Store settings
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'store_name'             => 'Es Teh Jumbo',
                'store_lat'              => -6.2434,
                'store_lng'              => 106.9871,
                'base_fare'              => 5000,
                'per_km_rate'            => 2000,
                'max_radius_km'          => 10,
                'min_order_for_delivery' => 30000,
            ]
        );

        // 4. Categories
        $teaSeries   = Category::updateOrCreate(['name' => 'Tea Series'],      ['sort_order' => 1]);
        $mojitoSeries= Category::updateOrCreate(['name' => 'Mojito Series'],   ['sort_order' => 2]);
        $lainnya     = Category::updateOrCreate(['name' => 'Minuman Lainnya'], ['sort_order' => 3]);
        $topping      = Category::updateOrCreate(['name' => 'Ekstra Topping'],   ['sort_order' => 4]);

        // 5. Ingredients
        $teh     = Ingredient::firstOrCreate(['name' => 'Daun Teh'],       ['unit' => 'g',   'current_stock' => 10000,'min_stock' => 1000,'cost_per_unit' => 15]);
        $gula    = Ingredient::firstOrCreate(['name' => 'Gula Pasir'],     ['unit' => 'g',   'current_stock' => 50000,'min_stock' => 5000,'cost_per_unit' => 18]);
        $susu    = Ingredient::firstOrCreate(['name' => 'Susu Cair'],      ['unit' => 'ml',  'current_stock' => 10000,'min_stock' => 1000,'cost_per_unit' => 15]);
        $cokelat  = Ingredient::firstOrCreate(['name' => 'Cokelat Bubuk'],  ['unit' => 'g',   'current_stock' => 2000, 'min_stock' => 200, 'cost_per_unit' => 60]);

        // 6. Products — TEA SERIES
        $this->makeProduct($teaSeries, "Teh Jumbo",         5000, 'images/es-teh-jumbo',        [[$teh, 10], [$gula, 20]]);
        $this->makeProduct($teaSeries, "Lemon Tea",         7000, 'images/lemon-tea-new',       [[$teh, 10], [$gula, 20]]);
        $this->makeProduct($teaSeries, "Apple Tea",         7000, 'images/es-teh-original',     [[$teh, 10], [$gula, 20]]);
        $this->makeProduct($teaSeries, "Blackcurrant Tea",  7000, 'images/blackcurrant-tea',    [[$teh, 10], [$gula, 20]]);
        $this->makeProduct($teaSeries, "Strawberry Tea",    7000, 'images/strawberry-tea',      [[$teh, 10], [$gula, 20]]);
        $this->makeProduct($teaSeries, "Milk Tea",          8000, 'images/milk-tea',             [[$teh, 10], [$gula, 15], [$susu, 50]]);
        $this->makeProduct($teaSeries, "Thai Tea",          8000, 'images/thai-tea',             [[$teh, 10], [$gula, 15], [$susu, 50]]);
        $this->makeProduct($teaSeries, "Chocolate Tea",     8000, 'images/chocolate-tea',            [[$teh, 10], [$gula, 15], [$cokelat, 10]]);
        $this->makeProduct($teaSeries, "Red Velvet Tea",    8000, 'images/red-velvet-tea',             [[$teh, 10], [$gula, 15]]);
        $this->makeProduct($teaSeries, "Matcha Tea",        8000, 'images/matcha-tea',             [[$teh, 10], [$gula, 15]]);

        // 7. Products — MOJITO SERIES
        $this->makeProduct($mojitoSeries, "Strawberry Soda", 8000, 'images/mojito-strawberry',   []);
        $this->makeProduct($mojitoSeries, "Grape Soda",      8000, 'images/mojito-grape',        []);
        $this->makeProduct($mojitoSeries, "Lychee Soda",     8000, 'images/mojito-lychee',       []);
        $this->makeProduct($mojitoSeries, "Mango Soda",      8000, 'images/mojito-mango',        []);
        $this->makeProduct($mojitoSeries, "Soursop Soda",    8000, 'images/mojito-soursop',      []);
        $this->makeProduct($mojitoSeries, "Orange Soda",     8000, 'images/mojito-orange',       []);
        $this->makeProduct($mojitoSeries, "Melon Soda",      8000, 'images/mojito-melon',        []);
        $this->makeProduct($mojitoSeries, "Lemon Soda",      8000, 'images/lemon-tea-new',       []);

        // 8. Products — MINUMAN LAINNYA
        $this->makeProduct($lainnya, "Es Coklat",          8000, 'images/es-coklat',       []);
        $this->makeProduct($lainnya, "Kopi Hitam",        5000, 'images/kopi-hitam',      []);
        $this->makeProduct($lainnya, "Top Cappuccino",     6000, 'images/cappuccino',      []);
        $this->makeProduct($lainnya, "Aneka Chocolatos",   6000, 'images/chocolatos',      []);
        $this->makeProduct($lainnya, "Beng Beng",          6000, 'images/beng-beng',       []);
        $this->makeProduct($lainnya, "Milo",               6000, 'images/milo',            []);
        $this->makeProduct($lainnya, "Extra Joss",         6000, 'images/extra-joss',      []);
        $this->makeProduct($lainnya, "Kuku Bima",          6000, 'images/kuku-bima',       []);
        $this->makeProduct($lainnya, "Aneka Pop Ice",      6000, 'images/pop-ice',         []);
        $this->makeProduct($lainnya, "Teh Gobak Sodoer",   6000, 'images/es-gobak-sodoer',         []);
        $this->makeProduct($lainnya, "Es Seger Mbok Sri",  6000, 'images/es-seger-mbok-sri',         []);
        $this->makeProduct($lainnya, "Melon Nipis",        8000, 'images/melon-nipis',     []);

        // 9. Products — EKSTRA TOPPING
        $this->makeProduct($topping, 'Jelly',  1000, 'images/topping-jelly',   []);
        $this->makeProduct($topping, 'Boba',   2000, 'images/topping-boba',    []);
        $this->makeProduct($topping, 'Messes', 1000, 'images/topping-messes',  []);
    }

    private function makeProduct(Category $cat, string $name, float $price, ?string $imagePath, array $recipeRows): Product
    {
        $product = Product::updateOrCreate(
            ['name' => $name],
            [
                'category_id'  => $cat->id,
                'description'  => null,
                'price'        => $price,
                'image_path'   => $imagePath,
                'is_available' => true,
            ],
        );

        foreach ($recipeRows as [$ingredient, $qty]) {
            Recipe::firstOrCreate(
                ['product_id' => $product->id, 'ingredient_id' => $ingredient->id],
                ['quantity_per_unit' => $qty],
            );
        }

        return $product;
    }
}
