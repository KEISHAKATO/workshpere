<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const ROLE_SEEKER   = 'seeker';
    public const ROLE_EMPLOYER = 'employer';
    public const ROLE_ADMIN    = 'admin';

    protected $attributes = [
        'role' => self::ROLE_SEEKER,
    ];

    public function isSeeker(): bool   { return $this->role === self::ROLE_SEEKER; }
    public function isEmployer(): bool { return $this->role === self::ROLE_EMPLOYER; }
    public function isAdmin(): bool    { return $this->role === self::ROLE_ADMIN; }
}
