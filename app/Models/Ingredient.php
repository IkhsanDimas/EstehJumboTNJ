<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'unit', 'current_stock', 'min_stock', 'cost_per_unit'];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'min_stock'     => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return (float) $this->current_stock <= (float) $this->min_stock;
    }
}
