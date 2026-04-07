<?php

namespace App\Models\HealthCenter;

use App\Models\Inventory\Item;
use Illuminate\Database\Eloquent\Model;

class HCPatientRequisitionItem extends Model
{
    protected $table = 'HCPatientRequisitionItem';
    protected $primaryKey = 'PRItemID';
    public $timestamps = false;

    protected $fillable = [
        'PatientReqID',
        'ItemID',
        'QuantityRequested',
    ];

    public function requisition()
    {
        return $this->belongsTo(HCPatientRequisition::class, 'PatientReqID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID');
    }
}
