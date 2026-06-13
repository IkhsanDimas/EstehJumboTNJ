<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private InventoryService $inventory,
        private ShippingCalculator $shipping,
    ) {
    }

    /**
     * Buat order POS (di tempat, bayar cash).
     * $items = [['product_id' => 1, 'quantity' => 2], ...]
     */
    public function createPosOrder(array $items, int $cashierId, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($items, $cashierId, $notes) {
            $this->assertProductsAvailable($items);

            $order = Order::create([
                'order_number'   => $this->generateOrderNumber(),
                'cashier_id'     => $cashierId,
                'type'           => 'pos',
                'status'         => 'pending',
                'payment_method' => 'cash',
                'notes'          => $notes,
            ]);

            $this->createOrderItems($order, $items);
            $this->recalculateTotals($order, ongkir: 0);

            return $order->fresh(['items']);
        });
    }

    /**
     * Buat order online: pickup atau delivery.
     */
    public function createOnlineOrder(
        array $items,
        ?int $userId,
        string $type,
        string $paymentMethod = 'cash',
        ?int $addressId = null,
        ?string $notes = null,
    ): Order {
        if (! in_array($type, ['online_pickup', 'online_delivery'], true)) {
            throw new \InvalidArgumentException("Tipe order online tidak valid: {$type}");
        }

        return DB::transaction(function () use ($items, $userId, $type, $paymentMethod, $addressId, $notes) {
            $this->assertProductsAvailable($items);

            $ongkir   = 0.0;
            $distance = null;

            if ($type === 'online_delivery') {
                if (! $addressId) {
                    throw new \InvalidArgumentException('Alamat pengantaran wajib untuk delivery.');
                }
                $address  = Address::findOrFail($addressId);
                $distance = $this->shipping->distanceFromStore((float) $address->latitude, (float) $address->longitude);
                $ongkir   = $this->shipping->calculateOngkir((float) $address->latitude, (float) $address->longitude);
            }

            $order = Order::create([
                'order_number'   => $this->generateOrderNumber(),
                'user_id'        => $userId,
                'type'           => $type,
                'status'         => 'pending',
                'payment_method' => $paymentMethod,
                'address_id'     => $type === 'online_delivery' ? $addressId : null,
                'distance_km'    => $distance,
                'notes'          => $notes,
            ]);

            $this->createOrderItems($order, $items);
            $this->recalculateTotals($order, ongkir: $ongkir);

            return $order->fresh(['items', 'address']);
        });
    }

    public function transitionStatus(Order $order, string $newStatus): Order
    {
        if (! $order->canTransitionTo($newStatus)) {
            throw new \DomainException(
                "Transisi status tidak diizinkan: {$order->status} → {$newStatus}"
            );
        }

        return DB::transaction(function () use ($order, $newStatus) {
            $order->status = $newStatus;

            if ($newStatus === 'paid') {
                $order->paid_at = now();
            }
            if ($newStatus === 'completed') {
                $order->completed_at = now();
            }
            if ($newStatus === 'preparing') {
                // Kebijakan: stok bahan baku dikurangi saat order mulai diproses.
                $this->inventory->deductStockForOrder($order);
            }

            $order->save();
            return $order;
        });
    }

    public function cancelOrder(Order $order): Order
    {
        return $this->transitionStatus($order, 'cancelled');
    }

    /* ------------------------------------------------------------------ */

    private function assertProductsAvailable(array $items): void
    {
        foreach ($items as $row) {
            $product = Product::findOrFail($row['product_id']);
            if (! $product->isAvailable((int) $row['quantity'])) {
                throw new InsufficientStockException(
                    "Produk {$product->name} tidak tersedia / stok tidak cukup."
                );
            }
        }
    }

    private function createOrderItems(Order $order, array $items): void
    {
        foreach ($items as $row) {
            $product = Product::findOrFail($row['product_id']);
            $qty     = (int) $row['quantity'];
            $price   = (float) $product->price;

            OrderItem::create([
                'order_id'              => $order->id,
                'product_id'            => $product->id,
                'product_name_snapshot' => $product->name,
                'price_snapshot'        => $price,
                'quantity'              => $qty,
                'line_total'            => $price * $qty,
            ]);
        }
    }

    private function recalculateTotals(Order $order, float $ongkir): void
    {
        $subtotal = (float) $order->items()->sum('line_total');
        $order->subtotal    = $subtotal;
        $order->ongkir      = $ongkir;
        $order->grand_total = $subtotal + $ongkir;
        $order->save();
    }

    private function generateOrderNumber(): string
    {
        return 'ETJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
    }
}
