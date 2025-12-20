<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'message',
        'referrer',
        'role',
    ];

    /**
     * Hidden attributes when serialized.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relacija: korisnik ima više poruka
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relacija: korisnik ima više zahteva za sajtove
     */
    public function siteRequests()
    {
        return $this->hasMany(FreeSiteRequest::class);
    }

    /**
     * Provera da li je korisnik admin ili superadmin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * Provera da li je superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
