<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\Requisition\Requisition;

class Adjustment extends Model
{
    protected $table = 'inventory_adjustments';
    protected $primaryKey = 'AdjustmentID';

    protected $fillable = [
        'BatchID',
        'RequisitionID',
        'AdjustmentType', // 'Disposal', 'Return', 'Correction'
        'QuantityAdjusted',
        'Reason',
        'Remarks',
        'EvidencePath',
        'AdjustedBy'
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
