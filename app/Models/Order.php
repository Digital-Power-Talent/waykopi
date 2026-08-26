<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $order_number
 * @property int|null $user_id
 * @property string|null $guest_email
 * @property string|null $guest_phone
 * @property string $recipient_name
 * @property string $recipient_phone
 * @property string $shipping_address
 * @property string|null $province
 * @property string|null $city
 * @property string|null $district
 * @property string|null $postal_code
 * @property float $subtotal
 * @property float $shipping_cost
 * @property string|null $voucher_code
 * @property float $discount_amount
 * @property int $unique_code
 * @property float $total
 * @property string $status
 * @property string|null $courier_name
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Shipment|null $shipment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderStatusHistory> $statusHistories
 */
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
        'voucher_code',
        'discount_amount',
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
        'discount_amount' => 'decimal:2',
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
