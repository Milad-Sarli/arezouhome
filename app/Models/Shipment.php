<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'is_default', 'default_price_rial', 'pay_after_delivery',
        'logo', 'description', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'default_price_rial' => 'integer',
            'pay_after_delivery' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class);
    }
}
