<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password',
        'role','is_active','is_flagged', // allow admin toggles
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_flagged'        => 'boolean',
        ];
    }

    public const ROLE_SEEKER   = 'seeker';
    public const ROLE_EMPLOYER = 'employer';
    public const ROLE_ADMIN    = 'admin';

    protected $attributes = [
        'role' => self::ROLE_SEEKER,
        "is_active" => true,
    ];

    public function isSeeker(): bool   { return $this->role === self::ROLE_SEEKER; }
    public function isEmployer(): bool { return $this->role === self::ROLE_EMPLOYER; }
    public function isAdmin(): bool    { return $this->role === self::ROLE_ADMIN; }

    // Relationships
    public function profile()          { return $this->hasOne(Profile::class); }
    public function jobs()             { return $this->hasMany(Job::class, 'employer_id'); }
    public function applications()     { return $this->hasMany(Application::class, 'seeker_id'); }
    public function messagesSent()     { return $this->hasMany(Message::class, 'sender_id'); }
    public function messagesReceived() { return $this->hasMany(Message::class, 'receiver_id'); }
}
