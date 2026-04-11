<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'records_synced',
        'message',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
