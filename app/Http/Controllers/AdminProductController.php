<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $store = StoreSetting::current();
        
        $query = Product::with('category')->latest();

        // Optional filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Optional search query
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.index', [
            'store'      => $store,
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only(['category_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $store = StoreSetting::current();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.create', [
            'store'      => $store,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255|unique:products,name',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:1000',
            'is_available' => 'required|boolean',
            'image'        => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
        ]);

        $imagePath = 'images/es-teh-original'; // default fallback path if no image uploaded

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'product_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('images/products'))) {
                mkdir(public_path('images/products'), 0755, true);
            }
            
            $file->move(public_path('images/products'), $filename);
            $imagePath = 'images/products/' . $filename;
        }

        Product::create([
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'price'        => $data['price'],
            'description'  => $data['description'],
            'is_available' => $data['is_available'],
            'image_path'   => $imagePath,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Menu minuman baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $store = StoreSetting::current();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.edit', [
            'store'      => $store,
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:1000',
            'is_available' => 'required|boolean',
            'image'        => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
        ]);

        $product->name = $data['name'];
        $product->category_id = $data['category_id'];
        $product->price = $data['price'];
        $product->description = $data['description'];
        $product->is_available = $data['is_available'];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'product_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('images/products'))) {
                mkdir(public_path('images/products'), 0755, true);
            }
            
            $file->move(public_path('images/products'), $filename);
            
            // Delete old custom file if it exists
            if ($product->image_path && str_starts_with($product->image_path, 'images/products/')) {
                @unlink(public_path($product->image_path));
            }
            
            $product->image_path = 'images/products/' . $filename;
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Menu minuman berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete image file if custom uploaded
            if ($product->image_path && str_starts_with($product->image_path, 'images/products/')) {
                @unlink(public_path($product->image_path));
            }

            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Menu minuman berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Gagal menghapus menu minuman. Menu ini masih terikat dengan transaksi/pesanan pelanggan.');
        }
    }
}
