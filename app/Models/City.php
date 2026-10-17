<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = ['province_id', 'name', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
