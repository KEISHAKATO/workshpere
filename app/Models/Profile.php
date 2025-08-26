<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    // OK for seeding/internal use; controllers must validate inputs.
    protected $guarded = [];

    protected $casts = [
        'skills' => 'array',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
