<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * SHOW CHECKOUT PAGE
     */
    public function index()
    {
        // GET CART
        $cart = session('cart', []);

        // ENSURE ARRAY
        if (!is_array($cart)) {
            $cart = [];
        }

        // GET STORE
        $store = StoreSetting::current();
        if (!$store->is_open) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Maaf, kedai kami saat ini sedang tutup. Silakan kembali lagi nanti.');
        }

        // EMPTY CART CHECK
        if (count($cart) <= 0) {

            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong');
        }

        // RETURN VIEW
        return view('checkout.index', [
            'cart' => $cart,
            'store' => $store,
        ]);
    }

    /**
     * STORE ORDER
     */
    public function store(Request $request)
    {
        // Check if store is open
        $store = StoreSetting::current();
        if (!$store->is_open) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Maaf, kedai kami saat ini sedang tutup. Transaksi tidak dapat diproses.');
        }

        // =========================
        // VALIDATION
        // =========================

        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],
            'delivery_type' => [
                'required',
                'string',
                'in:pickup,delivery',
            ],
            'address' => [
                'required_if:delivery_type,delivery',
                'nullable',
                'string',
                'max:1000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'latitude' => [
                'required_if:delivery_type,delivery',
                'nullable',
                'numeric',
            ],
            'longitude' => [
                'required_if:delivery_type,delivery',
                'nullable',
                'numeric',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:cash',
            ],
            'schedule_type' => [
                'required',
                'string',
                'in:now,later',
            ],
            'schedule_date' => [
                'required_if:schedule_type,later',
                'nullable',
                'date',
            ],
            'schedule_time' => [
                'required_if:schedule_type,later',
                'nullable',
                'string',
            ],
        ]);

        if ($validated['delivery_type'] === 'delivery') {
            if (empty($validated['latitude']) || empty($validated['longitude'])) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['delivery_type' => 'Lokasi pinpoint pada peta wajib ditandai untuk metode pengiriman ke alamat.']);
            }
        }

        // =========================
        // GET CART
        // =========================

        $cart = session('cart', []);

        // ENSURE ARRAY
        if (!is_array($cart)) {
            $cart = [];
        }

        // EMPTY CART CHECK
        if (count($cart) <= 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong');
        }

        // =========================
        // CALCULATE TOTAL (With Secure DB Validation)
        // =========================

        $subtotal = 0;
        foreach ($cart as $key => $item) {
            $product = \App\Models\Product::find($item['product_id'] ?? $item['id']);
            if (!$product || !$product->is_available) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Produk "' . ($item['name'] ?? '') . '" saat ini tidak tersedia atau telah dihapus.');
            }

            // Resolve size modifier
            $sizeKey = $item['size'] ?? 'reguler';
            $sizeModifier = 0;
            if ($sizeKey === 'jumbo') {
                $sizeModifier = 2000;
            }

            // Resolve toppings total from DB
            $toppingsTotal = 0;
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $toppingProduct = \App\Models\Product::find($topping['id']);
                    if (!$toppingProduct || !$toppingProduct->is_available || !$toppingProduct->isAvailable($item['quantity'])) {
                        return redirect()
                            ->route('cart.index')
                            ->with('error', 'Topping "' . ($topping['name'] ?? '') . '" saat ini tidak tersedia atau stok tidak mencukupi.');
                    }
                    $toppingsTotal += (int) $toppingProduct->price;
                }
            }

            // Recalculate price based on current DB values
            $recalculatedUnitPrice = (int) $product->price + $sizeModifier + $toppingsTotal;

            $subtotal += $recalculatedUnitPrice * (int) $item['quantity'];

            // Update cart item price in session snapshot
            $cart[$key]['price'] = $recalculatedUnitPrice;
            $cart[$key]['unit_price'] = $recalculatedUnitPrice;
        }

        // =========================
        // AGGREGATE INGREDIENT STOCK CHECK
        // =========================
        $aggregatedIngredients = [];
        foreach ($cart as $key => $item) {
            $product = \App\Models\Product::find($item['product_id'] ?? $item['id']);
            if (!$product) continue;

            $sizeKey = $item['size'] ?? 'reguler';
            $sizeMultiplier = ($sizeKey === 'jumbo') ? 1.5 : 1.0;

            // Calculate main product ingredients
            foreach ($product->recipes()->with('ingredient')->get() as $recipe) {
                if (!$recipe->ingredient) continue;
                $needed = (float) $recipe->quantity_per_unit * (int) $item['quantity'] * $sizeMultiplier;
                $ingId = $recipe->ingredient_id;
                $aggregatedIngredients[$ingId] = ($aggregatedIngredients[$ingId] ?? 0) + $needed;
            }

            // Calculate topping ingredients
            if (!empty($item['toppings'])) {
                foreach ($item['toppings'] as $topping) {
                    $toppingProduct = \App\Models\Product::find($topping['id']);
                    if (!$toppingProduct) continue;

                    foreach ($toppingProduct->recipes()->with('ingredient')->get() as $recipe) {
                        if (!$recipe->ingredient) continue;
                        $needed = (float) $recipe->quantity_per_unit * (int) $item['quantity'];
                        $ingId = $recipe->ingredient_id;
                        $aggregatedIngredients[$ingId] = ($aggregatedIngredients[$ingId] ?? 0) + $needed;
                    }
                }
            }
        }

        // Validate aggregate stock levels
        foreach ($aggregatedIngredients as $ingId => $totalNeeded) {
            $ingredient = \App\Models\Ingredient::find($ingId);
            if (!$ingredient || (float) $ingredient->current_stock < $totalNeeded) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Stok bahan baku (' . ($ingredient ? $ingredient->name : 'Bahan') . ') tidak mencukupi untuk memenuhi seluruh isi keranjang Anda.');
            }
        }

        // =========================
        // DELIVERY ELIGIBILITY & ONGKIR
        // =========================

        $store = StoreSetting::current();
        $ongkir = 0;
        $type = 'online_pickup';

        if ($validated['delivery_type'] === 'delivery') {
            $distance = 0;
            if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
                $lat1 = (float) $store->store_lat;
                $lng1 = (float) $store->store_lng;
                $lat2 = (float) $validated['latitude'];
                $lng2 = (float) $validated['longitude'];

                $earthRadius = 6371; // km
                $dLat = deg2rad($lat2 - $lat1);
                $dLng = deg2rad($lng2 - $lng1);

                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                     sin($dLng / 2) * sin($dLng / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
            }

            // Enforce max radius validation on server side
            if ($distance > (float) $store->max_radius_km) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['delivery_type' => 'Lokasi pengantaran terlalu jauh (Maksimal ' . $store->max_radius_km . ' km)']);
            }

            $baseFare = (int) $store->base_fare;
            $perKmRate = (int) $store->per_km_rate;
            $ongkir = $baseFare + (int) round($distance * $perKmRate);
            $type = 'online_delivery';
        }

        $grandTotal = $subtotal + $ongkir;

        // Save delivery schedule and address inside notes
        $finalNotes = $validated['notes'] ?? null;
        if ($validated['schedule_type'] === 'later') {
            $finalNotes = "Jadwal Pengantaran/Pengambilan: " . date('d F Y', strtotime($validated['schedule_date'])) . " pukul " . $validated['schedule_time'] . " WIB\n" . ($finalNotes ? "\n" . $finalNotes : '');
        }

        if ($validated['delivery_type'] === 'delivery') {
            $addressText = $validated['address'];
            if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
                $addressText .= "\nPinpoint Peta: https://www.google.com/maps?q=" . $validated['latitude'] . "," . $validated['longitude'];
            }
            $finalNotes = "Alamat Pengiriman:\n" . $addressText . "\n\n" . ($finalNotes ? "Catatan:\n" . $finalNotes : '');
        }

        // Prepend customer name to notes for database storage
        $finalNotes = "Nama Pelanggan: " . $validated['customer_name'] . "\n" . ($finalNotes ?: '');

        // =========================
        // CREATE ORDER & ITEMS IN TRANSACTION
        // =========================

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($type, $subtotal, $ongkir, $grandTotal, $validated, $finalNotes, $cart) {
            $order = Order::create([
                'order_number' => 'ETJ-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                'type' => $type,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'grand_total' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'notes' => $finalNotes,
            ]);

            foreach ($cart as $item) {
                $nameSnapshot = $item['name'];
                if (!empty($item['options_summary'])) {
                    $nameSnapshot .= " ({$item['options_summary']})";
                }
                if (!empty($item['notes'])) {
                    $nameSnapshot .= " [Catatan: {$item['notes']}]";
                }

                $toppingsIds = [];
                if (!empty($item['toppings'])) {
                    $toppingsIds = collect($item['toppings'])->pluck('id')->all();
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name_snapshot' => $nameSnapshot,
                    'price_snapshot' => $item['price'],
                    'quantity' => $item['quantity'],
                    'line_total' => (
                        (int) $item['price'] *
                        (int) $item['quantity']
                    ),
                    'metadata' => [
                        'size' => $item['size'] ?? 'reguler',
                        'toppings' => $toppingsIds,
                    ],
                ]);
            }

            return $order;
        });

        // Save order ID to session to allow viewing success page
        session(['last_order_id' => $order->id]);

        // =========================
        // WHATSAPP MESSAGE
        // =========================

        $msgText = "Halo Es Teh Jumbo!\n\n";
        $msgText .= "Saya ingin memesan:\n";

        foreach ($cart as $item) {
            $line = "- {$item['name']}";
            if (! empty($item['options_summary'])) {
                $line .= " ({$item['options_summary']})";
            }
            $line .= " x{$item['quantity']}\n";
            if (! empty($item['notes'])) {
                $line .= "  Catatan: {$item['notes']}\n";
            }
            $msgText .= $line;
        }

        $msgText .= "\n";
        $msgText .= "Detail Pesanan:\n";
        $msgText .= "- Tipe: " . ($validated['delivery_type'] === 'delivery' ? 'Kirim ke Alamat' : 'Ambil Sendiri') . "\n";
        
        if ($validated['schedule_type'] === 'later') {
            $msgText .= "- Jadwal: " . date('d F Y', strtotime($validated['schedule_date'])) . " " . $validated['schedule_time'] . " WIB\n";
        } else {
            $msgText .= "- Jadwal: " . ($validated['delivery_type'] === 'delivery' ? 'Kirim Sekarang (Instan)' : 'Ambil Sekarang') . "\n";
        }

        if ($validated['delivery_type'] === 'delivery') {
            $msgText .= "- Alamat: " . $validated['address'] . "\n";
            if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
                $msgText .= "- Pinpoint Peta: https://www.google.com/maps?q=" . $validated['latitude'] . "," . $validated['longitude'] . "\n";
            }
        }
        
        $msgText .= "- Pembayaran: Bayar di Tempat (COD)\n";
        $msgText .= "\n";

        $msgText .= "Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        if ($validated['delivery_type'] === 'delivery') {
            $msgText .= "Ongkir: Rp " . number_format($ongkir, 0, ',', '.') . "\n";
        }
        $msgText .= "Total: Rp " . number_format($grandTotal, 0, ',', '.') . "\n\n";

        $msgText .= "Order ID: {$order->order_number}\n";
        $msgText .= "Lacak Pesanan: " . route('order.track', ['order_number' => $order->order_number]) . "\n";
        $msgText .= "Nama: {$validated['customer_name']}\n";
        if (!empty($validated['notes'])) {
            $msgText .= "Catatan Tambahan: {$validated['notes']}\n";
        }

        // =========================
        // WHATSAPP URL
        // =========================

        $whatsappUrl = 'https://wa.me/' . config('services.whatsapp.number') . '?text=' . urlencode($msgText);

        // SAVE TO SESSION
        $request->session()->put(
            'whatsapp_url',
            $whatsappUrl
        );

        // =========================
        // CLEAR CART
        // =========================

        $request->session()->forget('cart');

        // =========================
        // REDIRECT SUCCESS PAGE
        // =========================

        return redirect()->route(
            'checkout.success',
            [
                'order' => $order->id,
            ]
        );
    }

    /**
     * SUCCESS PAGE
     */
    public function success(Order $order)
    {
        $store = StoreSetting::current();

        // Prevent IDOR: Check if this order belongs to current session or user is admin
        $allowed = false;
        if (session('last_order_id') == $order->id) {
            $allowed = true;
        } elseif (auth()->check() && auth()->user()->hasRole('owner')) {
            $allowed = true;
        }

        if (!$allowed) {
            abort(403, 'Akses ditolak. Anda tidak diperkenankan melihat pesanan ini.');
        }

        return view('checkout.success', [
            'order' => $order,
            'store' => $store,
        ]);
    }

    /**
     * TRACK ORDER PAGE
     */
    public function track(Request $request, $order_number = null)
    {
        $store = StoreSetting::current();
        
        if (!$order_number) {
            $order_number = $request->query('order_number');
        }

        $order = null;
        $error = null;

        if ($order_number) {
            $order_number = strtoupper(trim($order_number));
            $order = Order::with('items')->where('order_number', $order_number)->first();
            
            if (!$order) {
                $error = 'Pesanan dengan nomor ID "' . $order_number . '" tidak ditemukan. Silakan periksa kembali nomor pesanan Anda.';
            }
        }

        return view('checkout.track', [
            'order' => $order,
            'store' => $store,
            'error' => $error,
            'orderNumber' => $order_number,
        ]);
    }
}