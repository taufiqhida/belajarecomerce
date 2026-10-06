<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_note',
        'payment_method_id',
        'discount_code_id',
        'subtotal',
        'admin_fee',
        'discount_amount',
        'unique_code',
        'total',
        'status',
        'ordered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Batalkan pesanan -> stok kembali; aktifkan lagi -> stok dikurangi
        static::updated(function (Order $order) {
            if (!$order->wasChanged('status')) {
                return;
            }
            $was = $order->getOriginal('status');
            $now = $order->status;
            if ($now === 'cancelled' && $was !== 'cancelled') {
                $sign = 1;
            } elseif ($was === 'cancelled' && $now !== 'cancelled') {
                $sign = -1;
            } else {
                return;
            }
            foreach ($order->items as $item) {
                if ($item->product_id && (float) $item->weight_kg > 0) {
                    Product::whereKey($item->product_id)->increment('stock', $sign * (float) $item->weight_kg);
                }
                if ($item->flash_sale_id && (float) $item->flash_kg > 0) {
                    FlashSale::whereKey($item->flash_sale_id)->increment('flash_stock', $sign * (float) $item->flash_kg);
                }
            }
        });
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public static function generateOrderCode(): string
    {
        do {
            $code = 'TS-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('order_code', $code)->exists());
        return $code;
    }
}
