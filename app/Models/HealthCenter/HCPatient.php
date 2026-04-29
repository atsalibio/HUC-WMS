<?php

namespace App\Models\HealthCenter;

use App\Models\System\HealthCenter;
use Illuminate\Database\Eloquent\Model;

class HCPatient extends Model
{
    protected $table = 'HCPatient';
    protected $primaryKey = 'PatientID';
    public $timestamps = false;

    protected $fillable = [
        'FName',
        'MName',
        'LName',
        'Age',
        'Gender',
        'Address',
        'ContactNumber',
    ];

    public function requisitions()
    {
        return $this->hasMany(HCPatientRequisition::class, 'PatientID');
    }
}
