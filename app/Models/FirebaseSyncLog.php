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
        'records_added',
        'records_updated',
        'records_skipped',
        'records_failed',
        'total_records',
        'message',
        'started_at',
        'completed_at',
        'last_heartbeat_at',
        'process_name',
        'firebase_reads',
        'firebase_writes',
        'estimated_cost'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'estimated_cost' => 'decimal:6'
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($log) {
            $reads = (int)($log->firebase_reads ?? 0);
            $writes = (int)($log->firebase_writes ?? 0);
            // $0.06 per 100k reads = $0.0000006 per read
            // $0.18 per 100k writes = $0.0000018 per write
            $log->estimated_cost = ($reads * 0.0000006) + ($writes * 0.0000018);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
