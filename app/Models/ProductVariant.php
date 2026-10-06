<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'weight_kg',
        'price',
        'modal_price',
        'stock',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'modal_price' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /**
     * Total unit terjual dari pesanan yang sudah selesai.
     */
    public function soldCount(): int
    {
        return $this->orderItems()
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->sum('quantity');
    }
}
