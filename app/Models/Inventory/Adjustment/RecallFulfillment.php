<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\System\HealthCenter;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\Warehouse;

class RecallFulfillment extends Model
{
    protected $table = 'RecallFulfillment';
    protected $primaryKey = 'RecallFulfillmentID';
    public $timestamps = false;

    protected $fillable = [
        'ReferrenceNo',
        'RecallOrderID',
        'HCID',
        'BatchID',
        'HCBatchID',
        'ItemID',
        'EvidencePath',
        'QuantityFulfilled',
        'StatusType',
        'CreatedAt',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'WarehouseID');
    }

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

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID');
    }
}
