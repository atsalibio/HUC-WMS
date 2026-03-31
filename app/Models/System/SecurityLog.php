<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $table = 'SecurityLog';
    protected $primaryKey = 'SecurityLogID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'ActionType',
        'Description',
        'ActionDescription',
        'IPAddress',
        'ModuleAffected',
        'ActionDate',
    ];

    protected $casts = [
        'ActionDate' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
