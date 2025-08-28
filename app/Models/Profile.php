<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        // shared
        'bio',
        'about',
        'skills',
        'experience_years',
        'preferred_job_type',
        'availability',
        'location_city',
        'location_county',
        'lat',
        'lng',

        // employer
        'company_name',
        'website',
    ];

    protected $casts = [
        'skills' => 'array',
        'lat'    => 'decimal:7',
        'lng'    => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
