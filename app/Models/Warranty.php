<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'type', 'months', 'description', 'status'];

    protected function casts(): array
    {
        return ['months' => 'integer', 'status' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'official' => 'گارانتی شرکتی',
            'unofficial' => 'گارانتی متفرقه',
            default => $this->type ?? 'نامشخص',
        };
    }
}
