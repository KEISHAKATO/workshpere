<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function seeker()
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }
}
