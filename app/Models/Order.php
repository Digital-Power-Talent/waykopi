<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_email',
        'guest_phone',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'province',
        'city',
        'district',
        'postal_code',
        'subtotal',
        'shipping_cost',
        'unique_code',
        'total',
        'status',
        'courier_name',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'unique_code' => 'integer',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $randomSuffix = strtoupper(\Illuminate\Support\Str::random(4));
                $order->order_number = 'WK-' . now()->format('Ymd') . '-' . $randomSuffix;
            }
            if (empty($order->expires_at)) {
                $order->expires_at = now()->addHour();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * @return HasMany<OrderStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * @return HasMany<NotificationLog, $this>
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Update order status and record in status history.
     */
    public function recordStatusChange(string $toStatus, ?int $changedBy = null, ?string $note = null): OrderStatusHistory
    {
        $fromStatus = $this->status;
        $this->update(['status' => $toStatus]);

        /** @var OrderStatusHistory $history */
        $history = $this->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $changedBy,
            'note' => $note,
            'created_at' => now(),
        ]);

        return $history;
    }
}
