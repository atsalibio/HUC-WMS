<?php

namespace App\Models\HealthCenter;

use App\Models\System\User;
use App\Models\System\HealthCenter;
use Illuminate\Database\Eloquent\Model;

class HCPatientRequisition extends Model
{
    protected $table = 'HCPatientRequisition';
    protected $primaryKey = 'PatientReqID';
    public $timestamps = false;

    protected $fillable = [
        'PatientID',
        'UserID',
        'HealthCenterID',
        'RequisitionNumber',
        'RequestDate',
        'StatusType',
        'Diagnosis',
        'Notes',
        'ContactInfo',
        'IDProof',
    ];

    protected $casts = [
        'RequestDate' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(HCPatient::class, 'PatientID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class, 'HealthCenterID');
    }

    public function items()
    {
        return $this->hasMany(HCPatientRequisitionItem::class, 'PatientReqID');
    }
}
