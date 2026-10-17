<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'type', 'value_rial', 'max_uses', 'uses_count',
        'min_order_rial', 'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value_rial' => 'integer',
            'min_order_rial' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }
}
