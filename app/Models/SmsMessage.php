<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    protected $fillable = ['topic_id', 'body', 'status', 'created_by'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(SmsTopic::class, 'topic_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
