<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\System\HealthCenter;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\HealthCenter\HCInventoryBatch;

class InventoryHCCorrection extends Model
{
    protected $table = 'InventoryHCCorrection';
    protected $primaryKey = 'HCCorrectionID';
    public $timestamps = false;

    protected $fillable = [
        'HealthCenterID',
        'UserID',
        'BatchID',
        'HCBatchID',
        'ItemID',
        'QuantityCorrected',
        'QuantityBefore',
        'Reason',
        'EvidencePath',
        'StatusType',
        'CorrectionDate'
    ];

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class, 'HealthCenterID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'BatchID');
    }

    public function hcBatch()
    {
        return $this->belongsTo(HCInventoryBatch::class, 'HCBatchID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID');
    }
}
