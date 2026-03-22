<?php

namespace App\Models\System;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'Users'; 
    protected $primaryKey = 'UserID'; 
    protected $fillable = [
        'Username', 
        'Password', 
        'FName', 
        'MName', 
        'LName', 
        'Role', 
        'HealthCenterID'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['Password'] = Hash::make($value);
    }
}