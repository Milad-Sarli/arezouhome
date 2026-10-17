<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpec extends Model
{
    protected $fillable = ['product_id', 'product_attr_id', 'value'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attr(): BelongsTo
    {
        return $this->belongsTo(ProductAttr::class, 'product_attr_id');
    }
}
