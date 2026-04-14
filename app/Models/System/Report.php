<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;

class Report extends Model
{
    protected $table = 'report'; // Singular table name
    protected $primaryKey = 'ReportID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Using GeneratedDate instead of default timestamps if needed, but table listing had CreatedAt

    protected $fillable = [
        'ReportID',
        'UserID',
        'ReportType',
        'GeneratedDate',
        'GeneratedForOffice',
        'Data',
        'ReferenceID',
    ];

    protected $casts = [
        'Data' => 'array',
        'GeneratedDate' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
