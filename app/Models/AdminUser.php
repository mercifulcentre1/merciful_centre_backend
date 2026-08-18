<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admin_users';
    protected $fillable = ['username', 'email', 'full_name', 'password_hash', 'role', 'last_login'];
    protected $hidden = ['password_hash'];

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }
}
