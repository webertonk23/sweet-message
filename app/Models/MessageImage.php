<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageImage extends Model
{
    protected $fillable = [
        'message_id',
        'image_path'
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
} 