<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** Available size options. Modifier is added to base product price. */
    protected const SIZES = [
        'reguler' => ['label' => 'Reguler', 'modifier' => 0],
        'jumbo'   => ['label' => 'Jumbo',   'modifier' => 2000],
    ];

    protected const ICE_LABELS = [
        'less'   => 'Es sedikit',
        'normal' => 'Es normal',
        'extra'  => 'Es banyak',
        'none'   => 'Tanpa es',
    ];

    protected const SUGAR_LABELS = [
        'less'   => 'Less sugar',
        'normal' => 'Manis normal',
        'extra'  => 'Extra manis',
        'none'   => 'Tanpa gula',
    ];

    public function index()
    {
        $cart  = $this->normalize(session('cart', []));
        session(['cart' => $cart]);
        $store = StoreSetting::current();

        return view('cart.index', compact('cart', 'store'));
    }

    public function data(): JsonResponse
    {
        return response()->json($this->snapshot());
    }

    public function add(Request $request)
    {
        $product = Product::with('category')->findOrFail($request->product_id);

        // Resolve size
        $sizeKey   = $request->input('size', 'reguler');
        $size      = self::SIZES[$sizeKey] ?? self::SIZES['reguler'];
        $sizeKey   = isset(self::SIZES[$sizeKey]) ? $sizeKey : 'reguler';

        // Resolve toppings — only products from "Ekstra Topping" category
        $requestedToppingIds = collect((array) $request->input('toppings', []))
            ->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $toppings = $requestedToppingIds->isNotEmpty()
            ? Product::whereIn('id', $requestedToppingIds)
                ->whereHas('category', fn ($q) => $q->where('name', 'Ekstra Topping'))
                ->whereRaw('is_available = true')
                ->get()
                ->map(fn ($t) => [
                    'id'    => (int) $t->id,
                    'name'  => $t->name,
                    'price' => (int) $t->price,
                ])->values()->all()
            : [];

        // Levels (no price effect)
        $iceLevel   = $request->input('ice_level',   'normal');
        $sugarLevel = $request->input('sugar_level', 'normal');
        $iceLevel   = array_key_exists($iceLevel,   self::ICE_LABELS)   ? $iceLevel   : 'normal';
        $sugarLevel = array_key_exists($sugarLevel, self::SUGAR_LABELS) ? $sugarLevel : 'normal';

        // Per-item notes (max 200 chars)
        $notes = trim((string) $request->input('notes', ''));
        $notes = mb_substr($notes, 0, 200);

        // Quantity
        $qty = max(1, (int) $request->input('quantity', 1));

        if (! $product->isAvailable($qty)) {
            return $this->respondError($request, 'Stok tidak mencukupi');
        }

        // Compose key — include notes hash so different notes => different entry
        $toppingIdSig = collect($toppings)->pluck('id')->sort()->implode('-');
        $notesSig     = $notes !== '' ? substr(md5($notes), 0, 6) : '';
        $key = sprintf('p%d_%s_%s_%s_t%s_n%s',
            $product->id, $sizeKey, $iceLevel, $sugarLevel, $toppingIdSig, $notesSig
        );

        // Pricing
        $toppingsTotal = collect($toppings)->sum('price');
        $unitPrice     = (int) $product->price + (int) $size['modifier'] + (int) $toppingsTotal;

        // Summary text shown in drawer / WhatsApp
        $summary = $this->buildSummary($sizeKey, $iceLevel, $sugarLevel, $toppings);

        // Persist
        $cart = $this->normalize(session('cart', []));

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
        } else {
            $cart[$key] = [
                'key'             => $key,
                'product_id'      => $product->id,
                'name'            => $product->name,
                'image'           => $product->image_path,
                'base_price'      => (int) $product->price,
                'size'            => $sizeKey,
                'size_label'      => $size['label'],
                'size_modifier'   => (int) $size['modifier'],
                'ice_level'       => $iceLevel,
                'sugar_level'     => $sugarLevel,
                'toppings'        => $toppings,
                'unit_price'      => $unitPrice,
                'quantity'        => $qty,
                'notes'           => $notes,
                'options_summary' => $summary,
                // Legacy aliases so old views (cart.index, checkout) still work.
                'id'    => $product->id,
                'price' => $unitPrice,
            ];
        }

        session(['cart' => $cart]);

        return $this->respondSuccess($request, 'Ditambahkan ke keranjang');
    }

    public function update(Request $request)
    {
        $cart = $this->normalize(session('cart', []));
        $key  = $this->resolveKey($cart, $request);
        $action = $request->action;

        if ($key && isset($cart[$key])) {
            if ($action === 'increase') {
                $product = Product::find($cart[$key]['product_id']);
                if ($product && $product->isAvailable($cart[$key]['quantity'] + 1)) {
                    $cart[$key]['quantity']++;
                } else {
                    return $this->respondError($request, 'Stok tidak mencukupi');
                }
            } elseif ($action === 'decrease') {
                $cart[$key]['quantity']--;
                if ($cart[$key]['quantity'] <= 0) {
                    unset($cart[$key]);
                }
            }
            session(['cart' => $cart]);
        }

        return $this->respondSuccess($request);
    }

    public function remove(Request $request)
    {
        $cart = $this->normalize(session('cart', []));
        $key  = $this->resolveKey($cart, $request);

        if ($key && isset($cart[$key])) {
            unset($cart[$key]);
            session(['cart' => $cart]);
        }

        return $this->respondSuccess($request, 'Dihapus dari keranjang');
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /** Resolve the cart key from request: prefer `key`, fall back to legacy `product_id`. */
    protected function resolveKey(array $cart, Request $request): ?string
    {
        $key = $request->input('key');
        if ($key && isset($cart[$key])) {
            return $key;
        }

        // Legacy: client sent product_id only — match the first entry for that product.
        $productId = $request->input('product_id');
        if ($productId) {
            foreach ($cart as $k => $item) {
                if ((int) ($item['product_id'] ?? 0) === (int) $productId) {
                    return $k;
                }
            }
        }
        return null;
    }

    /** Convert any legacy cart entries (keyed by product_id, no options) to the new shape. */
    protected function normalize(array $cart): array
    {
        if (! is_array($cart)) {
            return [];
        }
        $out = [];
        foreach ($cart as $key => $item) {
            if (! is_array($item)) {
                continue;
            }
            if (isset($item['key']) && isset($item['unit_price'])) {
                $out[$item['key']] = $item;
                continue;
            }

            // Legacy entry → migrate.
            $productId = (int) ($item['id'] ?? $item['product_id'] ?? 0);
            if ($productId <= 0) continue;

            $unitPrice = (int) ($item['price'] ?? 0);
            $newKey = sprintf('p%d_reguler_normal_normal_t_n', $productId);
            $out[$newKey] = [
                'key'             => $newKey,
                'product_id'      => $productId,
                'name'            => $item['name']     ?? '—',
                'image'           => $item['image']    ?? '',
                'base_price'      => $unitPrice,
                'size'            => 'reguler',
                'size_label'      => 'Reguler',
                'size_modifier'   => 0,
                'ice_level'       => 'normal',
                'sugar_level'     => 'normal',
                'toppings'        => [],
                'unit_price'      => $unitPrice,
                'quantity'        => (int) ($item['quantity'] ?? 1),
                'notes'           => '',
                'options_summary' => '',
                'id'              => $productId,
                'price'           => $unitPrice,
            ];
        }
        return $out;
    }

    protected function buildSummary(string $size, string $ice, string $sugar, array $toppings): string
    {
        $parts = [];
        if ($size !== 'reguler') {
            $parts[] = self::SIZES[$size]['label'];
        }
        if ($ice !== 'normal') {
            $parts[] = self::ICE_LABELS[$ice];
        }
        if ($sugar !== 'normal') {
            $parts[] = self::SUGAR_LABELS[$sugar];
        }
        foreach ($toppings as $t) {
            $parts[] = '+' . $t['name'];
        }
        return implode(' · ', $parts);
    }

    protected function snapshot(): array
    {
        $cart = $this->normalize(session('cart', []));
        $items = collect($cart)->values()->map(function ($item) {
            $unit = (int) ($item['unit_price'] ?? $item['price'] ?? 0);
            $qty  = (int) ($item['quantity'] ?? 1);
            return [
                'key'             => $item['key'] ?? null,
                'id'              => (int) ($item['product_id'] ?? $item['id'] ?? 0),
                'name'            => $item['name'] ?? '—',
                'image'           => $item['image'] ?? '',
                'price'           => $unit,
                'quantity'        => $qty,
                'subtotal'        => $unit * $qty,
                'options_summary' => $item['options_summary'] ?? '',
                'notes'           => $item['notes'] ?? '',
            ];
        });

        return [
            'items' => $items,
            'count' => (int) $items->sum('quantity'),
            'total' => (int) $items->sum('subtotal'),
        ];
    }

    protected function respondSuccess(Request $request, ?string $message = null)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge(
                ['ok' => true, 'message' => $message],
                $this->snapshot()
            ));
        }
        return $message ? back()->with('success', $message) : back();
    }

    protected function respondError(Request $request, string $message, int $status = 422)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }
        return back()->with('error', $message);
    }
}
