<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'source', 'event_type', 'document_id', 'payload', 'status', 'message'
    ];

    protected $casts = [
        'payload' => 'array'
    ];
}
