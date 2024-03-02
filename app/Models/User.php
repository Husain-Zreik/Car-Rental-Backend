<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
    ];

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
