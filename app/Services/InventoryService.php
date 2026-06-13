<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Kurangi stok bahan baku sesuai resep tiap order item.
     * Dipanggil saat order naik ke status 'preparing' atau 'completed' (kebijakan: saat dimulai pembuatan).
     *
     * @throws InsufficientStockException
     */
    public function deductStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->with('product.recipes.ingredient')->get() as $item) {
                foreach ($item->product->recipes as $recipe) {
                    /** @var Ingredient $ingredient */
                    $ingredient = Ingredient::lockForUpdate()->find($recipe->ingredient_id);
                    $needed = (float) $recipe->quantity_per_unit * (int) $item->quantity;

                    if ((float) $ingredient->current_stock < $needed) {
                        throw new InsufficientStockException(
                            "Stok bahan {$ingredient->name} tidak cukup untuk produk {$item->product->name}."
                        );
                    }

                    $ingredient->current_stock = (float) $ingredient->current_stock - $needed;
                    $ingredient->save();

                    StockMovement::create([
                        'ingredient_id'  => $ingredient->id,
                        'type'           => 'out',
                        'quantity'       => $needed,
                        'reason'         => 'order_processed',
                        'reference_type' => Order::class,
                        'reference_id'   => $order->id,
                    ]);
                }
            }
        });
    }

    public function addStock(int $ingredientId, float $quantity, string $reason = 'manual_in'): StockMovement
    {
        return DB::transaction(function () use ($ingredientId, $quantity, $reason) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredientId);
            $ingredient->current_stock = (float) $ingredient->current_stock + $quantity;
            $ingredient->save();

            return StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type'          => 'in',
                'quantity'      => $quantity,
                'reason'        => $reason,
            ]);
        });
    }

    public function getLowStockItems(): Collection
    {
        return Ingredient::query()
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get();
    }
}
