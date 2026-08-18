<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property string|null $grind_type
 * @property int $weight_grams
 * @property float $price
 * @property int $stock
 * @property int $reserved_stock
 * @property bool $is_active
 * @property-read string $grind_type_label
 * @property-read Product $product
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'grind_type',
        'weight_grams',
        'price',
        'stock',
        'reserved_stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight_grams' => 'integer',
        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function grindTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match ($this->grind_type ?? '') {
                'whole_bean' => 'Biji Utuh',
                'fine' => 'Bubuk Halus',
                'medium' => 'Bubuk Sedang',
                'coarse' => 'Bubuk Kasar',
                default => ucfirst($this->grind_type ?? 'Utuh'),
            }
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function availableStock(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }
}
