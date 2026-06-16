<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'cashier_id', 'type', 'status',
        'subtotal', 'ongkir', 'grand_total', 'payment_method',
        'address_id', 'distance_km', 'notes', 'paid_at', 'completed_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'ongkir'       => 'decimal:2',
        'grand_total'  => 'decimal:2',
        'distance_km'  => 'decimal:3',
        'paid_at'      => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** Allowed transitions per state machine in design.md */
    public const TRANSITIONS = [
        'pending'    => ['preparing', 'paid', 'cancelled'],
        'preparing'  => ['ready', 'cancelled'],
        'ready'      => ['completed', 'paid', 'cancelled'],
        'paid'       => ['preparing', 'ready', 'completed', 'cancelled'],
        'completed'  => [],
        'cancelled'  => [],
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Get customer name dynamically.
     */
    public function getCustomerNameAttribute(): string
    {
        if ($this->user_id && $this->customer) {
            return $this->customer->name;
        }
        
        if ($this->notes && preg_match('/Nama Pelanggan:\s*([^\n\r]+)/i', $this->notes, $matches)) {
            return trim($matches[1]);
        }
        
        return 'Pelanggan';
    }
}
