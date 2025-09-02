<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    protected $fillable = [
        'job_id', 'sender_id', 'receiver_id', 'body', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /** Relationships  */
    public function job()     { return $this->belongsTo(Job::class); }
    public function sender()  { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver(){ return $this->belongsTo(User::class, 'receiver_id'); }

    /**
     * Scope: unread messages for a given receiver 
     */
    public function scopeUnreadFor(Builder $q, int $receiverId, ?int $jobId = null, ?int $senderId = null): Builder
    {
        $q->whereNull('read_at')
          ->where('receiver_id', $receiverId);

        if ($jobId) {
            $q->where('job_id', $jobId);
        }
        if ($senderId) {
            $q->where('sender_id', $senderId);
        }

        return $q;
    }
}
