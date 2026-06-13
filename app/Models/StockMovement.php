<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'ingredient_id', 'type', 'quantity', 'reason', 'reference_type', 'reference_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
