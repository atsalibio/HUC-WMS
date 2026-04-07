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
}
