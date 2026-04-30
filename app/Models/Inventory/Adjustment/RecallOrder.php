<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\System\HealthCenter;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\Warehouse;

class RecallOrder extends Model
{
    protected $table = 'RecallOrder';
    protected $primaryKey = 'RecallOrderID';
    public $timestamps = false;

    protected $fillable = [
        'ReferrenceNo',
        'UserID',
        'BatchID',
        'ItemID',
        'Reason',
        'QuantityOnRecall',
        'StatusType',
        'RecallDate'
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
