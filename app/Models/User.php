<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
