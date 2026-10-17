<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amazing extends Model
{
    protected $fillable = [
        'product_id', 'percent', 'price_rial', 'start_at', 'end_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price_rial' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isActive(): bool
    {
        if (! $this->status) {
            return false;
        }
        $now = now();
        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }
        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }
}
