<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $table = 'TransactionAuditLog';
    protected $primaryKey = 'AuditLogID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'ReferenceType',
        'ReferenceID',
        'ActionType',
        'ActionDetails',
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
