<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'job_posts';

    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'category',
        'job_type',
        'pay_min',
        'pay_max',
        'currency',
        'location_city',
        'location_county',
        'lat',
        'lng',
        'required_skills',
        'status',
        'posted_at',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'required_skills' => 'array',
        'lat'             => 'decimal:7',
        'lng'             => 'decimal:7',
        'posted_at'       => 'datetime',
    ];

    /**
     * Relationships
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'job_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'job_id');
    }
}
