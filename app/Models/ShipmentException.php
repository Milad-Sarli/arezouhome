<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    protected $fillable = ['shipment_id', 'province_id', 'city_ids', 'price_rial'];

    protected function casts(): array
    {
        return ['city_ids' => 'array', 'price_rial' => 'integer'];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
