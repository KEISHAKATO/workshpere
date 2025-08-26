<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $guarded = [];
    protected $table = 'job_posts';

    protected $casts = [
        'required_skills' => 'array',
        'lat' => 'float',
        'lng' => 'float',
        'posted_at' => 'datetime',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
