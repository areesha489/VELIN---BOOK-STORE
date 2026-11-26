<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'number', 'status', 'subtotal', 'shipping', 'total',
    'address_snapshot', 'payment_snapshot', 'tracking_code', 'carrier', 'notes',
])]
class Order extends Model
{
    public const STATUSES = [
        'placed' => 'Placed',
        'confirmed' => 'Confirmed',
        'packed' => 'Packed',
        'shipped' => 'Shipped',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const FLOW = [
        'placed',
        'confirmed',
        'packed',
        'shipped',
        'out_for_delivery',
        'delivered',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'address_snapshot' => 'array',
            'payment_snapshot' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('happened_at');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function paymentLabel(): string
    {
        $payment = $this->payment_snapshot ?? [];

        if (($payment['type'] ?? '') === 'upi') {
            return 'UPI · '.($payment['upi_id'] ?? '');
        }

        return trim(($payment['brand'] ?? 'Card').' ···· '.($payment['last_four'] ?? ''));
    }
}
