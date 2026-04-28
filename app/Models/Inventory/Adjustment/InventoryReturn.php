<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\System\HealthCenter;
use App\Models\Procurement\Warehouse;
use App\Models\Inventory\Batch;

class InventoryReturn extends Model
{
    protected $table = 'InventoryReturn';
    protected $primaryKey = 'ReturnID';
    public $timestamps = false;

    protected $fillable = [
        'HCID',
        'WarehouseID',
        'UserID',
        'HCBatchID',
        'BatchID',
        'ItemID',
        'QuantityReturned',
        'Reason',
        'EvidencePath',
        'StatusType',
        'ReturnDate'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'WarehouseID');
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class, 'HCID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'BatchID', 'BatchID');
    }
}
