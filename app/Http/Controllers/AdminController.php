<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * SHOW LOGIN FORM
     */
    public function showLoginForm()
    {
        // Redirect to dashboard if already logged in as admin
        if (Auth::check() && Auth::user()->hasRole('owner')) {
            return redirect()->route('admin.dashboard');
        }

        $store = StoreSetting::current();

        return view('admin.login', [
            'store' => $store,
        ]);
    }

    /**
     * PROCESS LOGIN
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Verify if user is authorized as admin (owner)
            if ($user->hasRole('owner')) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Reject customer role users
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda tidak memiliki hak akses administrator.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * PROCESS LOGOUT
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * SHOW DASHBOARD
     */
    public function dashboard()
    {
        $store = StoreSetting::current();
        
        // 1. Today's Sales (completed orders from today)
        $todaySales = \App\Models\Order::where('status', 'completed')
            ->whereDate('created_at', now()->toDateString())
            ->sum('grand_total');
        
        // 1b. Previous Sales (completed orders before today)
        $previousSales = \App\Models\Order::where('status', 'completed')
            ->whereDate('created_at', '<', now()->toDateString())
            ->sum('grand_total');
        
        // 2. Total Orders (not cancelled)
        $totalOrdersCount = \App\Models\Order::where('status', '!=', 'cancelled')->count();
        
        // 3. Ingredients Stock
        $ingredients = \App\Models\Ingredient::all();
        $tea = $ingredients->firstWhere('name', 'Daun Teh');
        $teaStock = $tea ? $tea->current_stock : 10000;
        $teaMinStock = $tea ? $tea->min_stock : 1000;
        
        // 4. Active Orders (Live)
        $activeOrders = \App\Models\Order::with('items')->whereIn('status', ['pending', 'paid', 'preparing', 'ready'])
            ->latest()
            ->get();
            
        // 5. Past Orders (History) with date filter
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));
        
        $pastOrders = \App\Models\Order::with('items')->whereIn('status', ['completed', 'cancelled'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();
        
        // 6. 7-Day Sales History (completed orders)
        $salesData = \App\Models\Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function($group) {
                return (float) $group->sum('grand_total');
            });
            
        $salesHistoryLabels = [];
        $salesHistoryValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $salesHistoryLabels[] = $dateObj->translatedFormat('d M');
            $salesHistoryValues[] = (float) ($salesData[$dateStr] ?? 0);
        }
        
        // 7. Top 5 Varian Terlaris
        $topProductsQuery = \App\Models\OrderItem::selectRaw('product_name_snapshot as name, SUM(quantity) as total')
            ->groupBy('product_name_snapshot')
            ->orderByDesc('total')
            ->take(5)
            ->get();
            
        $topProductsLabels = $topProductsQuery->pluck('name')->toArray();
        $topProductsLabels = array_map(function($name) {
            return explode(' (', $name)[0];
        }, $topProductsLabels);
        
        $topProductsValues = $topProductsQuery->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // 8. Stock Movements for Inventory Log
        $stockMovements = \App\Models\StockMovement::with('ingredient')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.dashboard', [
            'store'            => $store,
            'user'             => Auth::user(),
            'todaySales'       => $todaySales,
            'previousSales'    => $previousSales,
            'totalOrdersCount' => $totalOrdersCount,
            'teaStock'         => $teaStock,
            'teaMinStock'      => $teaMinStock,
            'ingredients'      => $ingredients,
            'activeOrders'     => $activeOrders,
            'pastOrders'       => $pastOrders,
            'recentOrders'     => $activeOrders->concat($pastOrders)->take(10),
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'salesLabels'      => $salesHistoryLabels,
            'salesValues'      => $salesHistoryValues,
            'topLabels'        => $topProductsLabels,
            'topValues'        => $topProductsValues,
            'stockMovements'   => $stockMovements,
        ]);
    }

    /**
     * UPDATE ORDER STATUS
     */
    public function updateOrderStatus(Request $request, \App\Models\Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,paid,preparing,ready,completed,cancelled',
        ]);

        $newStatus = $data['status'];

        // Enforce transitions using the model rule
        if (!$order->canTransitionTo($newStatus)) {
            return back()->withErrors([
                'status' => 'Transisi status pesanan dari ' . $order->status . ' ke ' . $newStatus . ' tidak diperbolehkan.',
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $newStatus) {
            $order->status = $newStatus;

            if ($newStatus === 'paid') {
                $order->paid_at = now();
            } elseif ($newStatus === 'completed') {
                $order->completed_at = now();
                if (is_null($order->paid_at)) {
                    $order->paid_at = now();
                }

                // Deduct stock of ingredients based on recipes of the ordered products & toppings
                foreach ($order->items()->get() as $item) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $metadata = $item->metadata ?? [];
                        $sizeKey = $metadata['size'] ?? 'reguler';
                        $sizeMultiplier = ($sizeKey === 'jumbo') ? 1.5 : 1.0;

                        // Deduct product ingredients
                        foreach ($product->recipes()->with('ingredient')->get() as $recipe) {
                            $totalQtyUsed = (float) $recipe->quantity_per_unit * (int) $item->quantity * $sizeMultiplier;
                            
                            $ingredient = $recipe->ingredient;
                            if ($ingredient) {
                                // Deduct current stock
                                $ingredient->decrement('current_stock', $totalQtyUsed);

                                // Log stock movement
                                \App\Models\StockMovement::create([
                                    'ingredient_id' => $ingredient->id,
                                    'type' => 'out',
                                    'quantity' => $totalQtyUsed,
                                    'reason' => "Pesanan #{$order->order_number} selesai (" . ucfirst($sizeKey) . ")",
                                    'reference_type' => \App\Models\Order::class,
                                    'reference_id' => $order->id,
                                ]);
                            }
                        }

                        // Deduct topping ingredients
                        $toppingsIds = $metadata['toppings'] ?? [];
                        foreach ($toppingsIds as $toppingId) {
                            $toppingProduct = \App\Models\Product::find($toppingId);
                            if ($toppingProduct) {
                                foreach ($toppingProduct->recipes()->with('ingredient')->get() as $recipe) {
                                    $toppingQtyUsed = (float) $recipe->quantity_per_unit * (int) $item->quantity;
                                    
                                    $ingredient = $recipe->ingredient;
                                    if ($ingredient) {
                                        // Deduct current stock
                                        $ingredient->decrement('current_stock', $toppingQtyUsed);

                                        // Log stock movement
                                        \App\Models\StockMovement::create([
                                            'ingredient_id' => $ingredient->id,
                                            'type' => 'out',
                                            'quantity' => $toppingQtyUsed,
                                            'reason' => "Topping {$toppingProduct->name} untuk pesanan #{$order->order_number} selesai",
                                            'reference_type' => \App\Models\Order::class,
                                            'reference_id' => $order->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $order->save();
        });

        return back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diubah menjadi ' . $newStatus . '!');
    }

    /**
     * GET LATEST ORDER ID & COUNT FOR POLLING
     */
    public function getLatestOrderId()
    {
        $latestOrder = \App\Models\Order::latest()->first();
        return response()->json([
            'latest_id' => $latestOrder ? $latestOrder->id : 0,
            'count' => \App\Models\Order::count(),
        ]);
    }

    /**
     * TOGGLE GLOBAL STORE SETTING (is_open / busy_mode)
     */
    public function toggleStoreSetting(Request $request)
    {
        $data = $request->validate([
            'setting' => 'required|string|in:is_open,busy_mode',
        ]);

        $store = StoreSetting::current();
        $setting = $data['setting'];
        
        $store->$setting = !$store->$setting;
        $store->save();

        return response()->json([
            'ok' => true,
            'setting' => $setting,
            'value' => (bool) $store->$setting,
            'message' => 'Pengaturan ' . ($setting === 'is_open' ? 'Buka Toko' : 'Banner Promosi') . ' berhasil diubah.'
        ]);
    }

    /**
     * UPDATE VISUAL SETTINGS
     */
    public function updateVisualSettings(Request $request)
    {
        $store = StoreSetting::current();

        $data = $request->validate([
            'font_family'        => 'required|string|in:Plus Jakarta Sans,Inter,Outfit,Poppins,Montserrat,Playfair Display',
            'hero_title'         => 'required|string|max:1000',
            'hero_subtitle'      => 'required|string|max:2000',
            'about_text'         => 'required|string|max:4000',
            'promo_banner_text'  => 'required|string|max:1000',
            'hero_image'         => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
            'about_image'        => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
        ]);

        $store->font_family = $data['font_family'];
        $store->hero_title = $data['hero_title'];
        $store->hero_subtitle = $data['hero_subtitle'];
        $store->about_text = $data['about_text'];
        $store->promo_banner_text = $data['promo_banner_text'];

        // Handle Hero Image Upload
        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create uploads directory if not exists
            if (!file_exists(public_path('images/uploads'))) {
                mkdir(public_path('images/uploads'), 0755, true);
            }
            
            $file->move(public_path('images/uploads'), $filename);
            
            // Delete old file if custom uploaded
            if ($store->hero_image_path && str_starts_with($store->hero_image_path, 'images/uploads/')) {
                @unlink(public_path($store->hero_image_path));
            }
            
            $store->hero_image_path = 'images/uploads/' . $filename;
        }

        // Handle About Image Upload
        if ($request->hasFile('about_image')) {
            $file = $request->file('about_image');
            $filename = 'about_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create uploads directory if not exists
            if (!file_exists(public_path('images/uploads'))) {
                mkdir(public_path('images/uploads'), 0755, true);
            }
            
            $file->move(public_path('images/uploads'), $filename);
            
            // Delete old file if custom uploaded
            if ($store->about_image_path && str_starts_with($store->about_image_path, 'images/uploads/')) {
                @unlink(public_path($store->about_image_path));
            }
            
            $store->about_image_path = 'images/uploads/' . $filename;
        }

        $store->save();

        return back()->with('success', 'Tampilan visual website berhasil diperbarui!');
    }

    /**
     * REPLENISH INGREDIENT STOCK
     */
    public function replenishIngredient(Request $request, \App\Models\Ingredient $ingredient)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($ingredient, $data) {
            $ingredient->increment('current_stock', (float) $data['quantity']);

            \App\Models\StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'in',
                'quantity' => (float) $data['quantity'],
                'reason' => 'Penambahan stok oleh admin',
            ]);
        });

        return back()->with('success', 'Stok bahan ' . $ingredient->name . ' berhasil ditambahkan sebanyak ' . number_format($data['quantity'], 0, ',', '.') . ' ' . $ingredient->unit . '!');
    }

    /**
     * UPDATE INGREDIENT DETAILS
     */
    public function updateIngredient(Request $request, \App\Models\Ingredient $ingredient)
    {
        $data = $request->validate([
            'min_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);

        $ingredient->update([
            'min_stock' => (float) $data['min_stock'],
            'cost_per_unit' => (float) $data['cost_per_unit'],
        ]);

        return back()->with('success', 'Pengaturan bahan ' . $ingredient->name . ' berhasil diperbarui!');
    }

    /**
     * CHANGE PASSWORD FOR ADMIN
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required'     => 'Password baru wajib diisi.',
            'new_password.min'          => 'Password baru minimal harus 8 karakter.',
            'new_password.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Refresh session auth to keep user logged in
        Auth::login($user);

        return back()->with('success', 'Password administrator berhasil diperbarui!');
    }
}
