<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudSyncCheckpoint extends Model
{
    protected $fillable = [
        'process_name',
        'sync_type',
        'status',
        'last_successful_sync_at',
        'last_document_id',
        'last_firebase_updated_at',
        'records_processed',
        'records_synced',
        'records_failed',
        'estimated_reads',
        'error_message',
        'retry_count',
        'user_id',
        'started_at',
        'finished_at',
        'duration_seconds'
    ];

    protected $casts = [
        'last_successful_sync_at' => 'datetime',
        'last_firebase_updated_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
