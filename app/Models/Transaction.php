<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'order_id', 'authority', 'ref_id', 'amount_rial', 'gateway',
        'status', 'installment_count', 'installment_amount_rial', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount_rial' => 'integer',
            'installment_amount_rial' => 'integer',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function gatewayLabel(): string
    {
        return match ($this->gateway) {
            'saman' => 'پرداخت آنلاین (سامان)',
            'zarinpal' => 'پرداخت آنلاین (زرین‌پال)',
            'digipay' => 'اقساطی دیجی‌پی',
            'torobpay' => 'اقساطی ترب‌پی',
            null, '' => 'نامشخص',
            default => $this->gateway,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'start_payment' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'failed' => 'ناموفق',
            'canceled' => 'لغو شده',
            default => 'نامشخص',
        };
    }

    public function isInstallment(): bool
    {
        return in_array($this->gateway, ['digipay', 'torobpay'], true);
    }
}
