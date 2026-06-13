<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image_path', 'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipes')
            ->withPivot('quantity_per_unit')
            ->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Hitung berapa banyak produk ini yang masih bisa dibuat berdasarkan stok bahan.
     */
    public function maxBuildable(): int
    {
        $recipes = $this->recipes()->with('ingredient')->get();
        if ($recipes->isEmpty()) {
            return PHP_INT_MAX;
        }

        $maxes = $recipes->map(function (Recipe $r) {
            $qty = (float) $r->quantity_per_unit;
            if ($qty <= 0 || !$r->ingredient) {
                return PHP_INT_MAX;
            }
            return (int) floor(((float) $r->ingredient->current_stock) / $qty);
        });

        return (int) $maxes->min();
    }

    public function isAvailable(int $quantity = 1): bool
    {
        return $this->is_available && $this->maxBuildable() >= $quantity;
    }
}
