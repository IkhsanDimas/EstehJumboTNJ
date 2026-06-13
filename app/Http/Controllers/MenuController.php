<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with(['products' => fn ($q) => $q->whereRaw('is_available = true')->orderBy('name')])
            ->orderBy('sort_order')
            ->get();

        $store = StoreSetting::current();

        return view('menu.index', compact('categories', 'store'));
    }

    public function menu(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));

        $categoriesQuery = Category::query()
            ->with(['products' => function ($q) use ($query) {
                $q->whereRaw('is_available = true');
                if ($query !== '') {
                    $q->where('name', 'like', '%' . $query . '%');
                }
                $q->orderBy('name');
            }])
            ->orderBy('sort_order');

        $categories = $categoriesQuery->get();
        $store      = StoreSetting::current();

        return view('menu.catalog', compact('categories', 'store', 'query'));
    }

    public function show(Product $product): View
    {
        // Block topping items from having a stand-alone detail page —
        // they exist only as add-ons.
        if (optional($product->category)->name === 'Ekstra Topping') {
            abort(404);
        }

        $product->load(['category', 'ingredients']);

        // Available toppings (live from DB)
        $toppings = Product::with('category')
            ->whereHas('category', fn ($q) => $q->where('name', 'Ekstra Topping'))
            ->whereRaw('is_available = true')
            ->orderBy('price')
            ->get();

        // Related products (same category, exclude self)
        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereRaw('is_available = true')
            ->inRandomOrder()
            ->take(4)
            ->get();

        $store = StoreSetting::current();

        $sizes = [
            ['key' => 'reguler', 'label' => 'Reguler', 'modifier' => 0,    'volume' => '350 ml'],
            ['key' => 'jumbo',   'label' => 'Jumbo',   'modifier' => 2000, 'volume' => '500 ml'],
        ];

        return view('menu.show', compact('product', 'toppings', 'related', 'store', 'sizes'));
    }

    public function productData(Product $product): JsonResponse
    {
        $product->load('category');
        $toppings = Product::whereHas('category', fn ($q) => $q->where('name', 'Ekstra Topping'))
            ->whereRaw('is_available = true')
            ->orderBy('price')
            ->get();
        
        $sizes = [
            ['key' => 'reguler', 'label' => 'Reguler', 'modifier' => 0,    'volume' => '350 ml'],
            ['key' => 'jumbo',   'label' => 'Jumbo',   'modifier' => 2000, 'volume' => '500 ml'],
        ];

        return response()->json([
            'product'  => $product,
            'toppings' => $toppings,
            'sizes'    => $sizes,
        ]);
    }
}
