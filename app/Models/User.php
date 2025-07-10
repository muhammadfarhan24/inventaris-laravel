<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
     public $timestamps = false;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user'; // karena kamu pakai nama tabel 'user' bukan 'users'

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Override agar login pakai username
    public function username()
    {
        return 'username';
    }
}
