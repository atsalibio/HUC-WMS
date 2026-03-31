<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'Requisition';
    protected $primaryKey = 'RequisitionID';
    public $timestamps = false;

    protected $fillable = [
        'RequisitionNumber',
        'HealthCenterID',
        'UserID',
        'RequestDate',
        'StatusType'
    ];

    protected $casts = [
        'RequestDate' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(RequisitionItem::class, 'RequisitionID');
    }

    public function healthCenter()
    {
        return $this->belongsTo(\App\Models\System\HealthCenter::class, 'HealthCenterID');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\System\User::class, 'UserID');
    }
}
