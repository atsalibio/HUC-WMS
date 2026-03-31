<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'CentralInventoryBatch';
    protected $primaryKey = 'BatchID';
    public $timestamps = false;

    protected $fillable = [
        'LotNumber',
        'ItemID',
        'WarehouseID',
        'ExpiryDate',
        'QuantityOnHand',
        'QuantityReleased',
        'UnitCost',
        'DateReceived',
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
