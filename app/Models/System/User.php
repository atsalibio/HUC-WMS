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

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    public function setPasswordAttribute($value)
    {
        $this->attributes['Password'] = Hash::make($value);
    }

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getAuthPasswordName()
    {
        return 'Password';
    }

    public function healthCenter()
    {
        return $this->belongsTo(\App\Models\System\HealthCenter::class, 'HealthCenterID', 'HealthCenterID');
    }
}