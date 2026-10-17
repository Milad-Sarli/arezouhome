<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'down_payment_percent', 'months', 'fee_percent', 'description', 'status',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }
}
