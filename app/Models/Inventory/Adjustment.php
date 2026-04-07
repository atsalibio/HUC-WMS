<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\Requisition\Requisition;

class Adjustment extends Model
{
    protected $table = 'InventoryAdjustment';
    protected $primaryKey = 'AdjustmentID';
    public $timestamps = false;

    protected $fillable = [
        'BatchID',
        'UserID',
        'AdjustmentType',
        'AdjustmentQuantity',
        'Reason',
        'EvidencePath',
        'RequisitionID',
        'AdjustmentDate'
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'BatchID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'AdjustedBy');
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'RequisitionID');
    }
}
