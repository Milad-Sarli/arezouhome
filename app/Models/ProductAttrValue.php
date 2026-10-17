<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttrValue extends Model
{
    protected $fillable = ['product_attr_id', 'value', 'sort'];

    public function attr(): BelongsTo
    {
        return $this->belongsTo(ProductAttr::class, 'product_attr_id');
    }
}
