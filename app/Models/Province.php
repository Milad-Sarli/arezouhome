<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['name', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
