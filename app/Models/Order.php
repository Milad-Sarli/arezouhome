<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'address_id', 'shipment_id', 'discount_code_id', 'installment_plan_id',
        'ref_code', 'status', 'post_price_rial', 'discount_price_rial', 'final_price_rial', 'note',
    ];

    protected function casts(): array
    {
        return [
            'post_price_rial' => 'integer',
            'discount_price_rial' => 'integer',
            'final_price_rial' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function installmentPlan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function paidTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->where('status', 'paid');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل داده شده',
            'canceled' => 'لغو شده',
            'returned' => 'مرجوع شده',
            default => 'نامشخص',
        };
    }
}
