<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Item;
use App\Models\Procurement\Warehouse;

class Batch extends Model
{
    protected $table = 'CentralInventoryBatch';
    protected $primaryKey = 'BatchID';
    public $timestamps = false;

    protected $fillable = [
        'LotNumber',
        'BatchNumber',
        'ItemID',
        'WarehouseID',
        'ExpiryDate',
        'QuantityOnHand',
        'QuantityReleased',
        'IsLocked',
        'UnitCost',
        'LastUpdated',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'WarehouseID');
    }
}
