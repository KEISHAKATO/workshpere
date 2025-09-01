<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'job_id',
        'sender_id',
        'receiver_id',
        'body',
        'read_at',
        // 'content' exists in DB, but we don't use it; leaving it out is fine.
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function job()      { return $this->belongsTo(Job::class); }
    public function sender()   { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
}
