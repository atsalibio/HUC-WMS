<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class HealthCenter extends Model
{
    protected $table = 'HealthCenters';
    protected $primaryKey = 'HealthCenterID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Location',
        'Code', // optional
    ];
}