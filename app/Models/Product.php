<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'images',
        'base_price',
        'modal_price',
        'stock',
        'badge',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'stock' => 'decimal:2',
        'base_price' => 'decimal:2',
        'modal_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class);
    }

    public function activeFlashSale()
    {
        return $this->flashSales()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    public function getBadgeLabelAttribute(): string
    {
        return match ($this->badge) {
            'best_seller' => 'Best Seller',
            'new' => 'Baru',
            'promo' => 'Promo',
            'limited' => 'Limited',
            default => '',
        };
    }
}
