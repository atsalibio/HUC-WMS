<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\Warehouse;

class InventoryWarehouseCorrection extends Model
{
    protected $table = 'InventoryWarehouseCorrection';
    protected $primaryKey = 'WarehouseCorrectionID';
    public $timestamps = false;

    protected $fillable = [
        'WarehouseID',
        'UserID',
        'BatchID',
        'ItemID',
        'QuantityBefore',
        'QuantityCorrected',
        'Reason',
        'EvidencePath',
        'StatusType',
        'CorrectionDate'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'WarehouseID');
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
