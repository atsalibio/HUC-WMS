<?php

namespace App\Models\Issuance;

use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    protected $table = 'Issuance';
    protected $primaryKey = 'IssuanceID';
    public $timestamps = false;

    protected $fillable = [
        'RequisitionID',
        'UserID',
        'IssueDate',
        'StatusType'
    ];

    public function items()
    {
        return $this->hasMany(IssuanceItem::class, 'IssuanceID', 'IssuanceID');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\System\User::class, 'UserID');
    }

    public function requisition()
    {
        return $this->belongsTo(\App\Models\Requisition\Requisition::class, 'RequisitionID');
    }
}
