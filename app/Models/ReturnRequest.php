<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'product_id', 'quantity', 'reason', 'status', 'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'در انتظار بررسی',
            'approved' => 'تأیید شده',
            'rejected' => 'رد شده',
            'completed' => 'تکمیل شده',
            default => 'نامشخص',
        };
    }
}
