<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsTopic extends Model
{
    protected $fillable = ['title', 'description'];

    public function messages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'topic_id');
    }
}
