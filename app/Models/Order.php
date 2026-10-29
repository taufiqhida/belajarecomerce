<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** Batas bayar sejak pesanan dibuat / sejak ongkir ditentukan admin */
    public const PAYMENT_WINDOW_MINUTES = 60;
    /** Pesanan yang menunggu ongkir diberi waktu lebih panjang agar admin sempat menentukan */
    public const SHIPPING_WAIT_HOURS = 24;

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_address',
        'delivery_method',
        'shipping_zone_id',
        'shipping_zone_name',
        'shipping_fee',
        'shipping_pending',
        'payment_due_at',
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
        'shipping_fee' => 'decimal:2',
        'shipping_pending' => 'boolean',
        'payment_due_at' => 'datetime',
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
            if ($order->discount_code_id) {
                // batal -> jatah kode diskon kembali; aktif lagi -> terpakai lagi
                $code = DiscountCode::find($order->discount_code_id);
                if ($sign === 1 && $code && $code->used_count > 0) {
                    $code->decrement('used_count');
                } elseif ($sign === -1 && $code) {
                    $code->increment('used_count');
                }
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

    /**
     * Admin menentukan ongkir untuk pesanan yang daerahnya belum punya tarif.
     * Total dihitung ulang dan batas bayar dimulai dari sekarang.
     */
    public function applyShippingFee(float $fee): void
    {
        if ($fee < 0) {
            throw new \InvalidArgumentException('Ongkir tidak boleh negatif.');
        }
        if (in_array($this->status, ['cancelled', 'completed'], true) || $this->delivery_method === 'pickup') {
            throw new \DomainException('Ongkir tidak bisa diubah untuk pesanan ini.');
        }

        $this->shipping_fee = $fee;
        $this->shipping_zone_name = $this->shipping_zone_name ?: 'Ditentukan admin';
        $this->total = (float) $this->subtotal + (float) $this->admin_fee + $fee - (float) $this->discount_amount + (int) $this->unique_code;
        if ($this->shipping_pending) {
            $this->shipping_pending = false;
            $this->payment_due_at = now()->addMinutes(self::PAYMENT_WINDOW_MINUTES);
        }
        $this->save();
    }

    /** Tautan nota untuk pembeli (bertanda tangan, tidak bergantung host/skema) */
    public function customerNotaUrl(): string
    {
        return url(\Illuminate\Support\Facades\URL::signedRoute('nota.customer', $this->order_code, null, false));
    }

    /** wa.me ke pembeli berisi ongkir, total, dan tautan invoice */
    public function invoiceWhatsappUrl(): string
    {
        $phone = preg_replace('/\D/', '', (string) $this->customer_phone);
        $phone = preg_match('/^0/', $phone) ? '62' . substr($phone, 1) : $phone;

        $store = StoreSetting::current()->store_name ?? 'Cold Storage';
        $fmt = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
        $lines = [
            "Halo {$this->customer_name}, terima kasih sudah memesan di {$store}.",
            '',
            "📋 Pesanan: {$this->order_code}",
            "💰 Subtotal: " . $fmt($this->subtotal),
        ];
        if ((float) $this->shipping_fee > 0) {
            $lines[] = "🚚 Ongkir: " . $fmt($this->shipping_fee);
        }
        $lines[] = "💵 *TOTAL BAYAR: " . $fmt($this->total) . "*";
        if ($this->paymentMethod) {
            $lines[] = "💳 Pembayaran: {$this->paymentMethod->name}";
        }
        $lines[] = '';
        $lines[] = "Invoice lengkap: " . $this->customerNotaUrl();
        $lines[] = '';
        $lines[] = "Mohon transfer sesuai total di atas. Terima kasih 🙏";

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode(implode("\n", $lines));
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
