<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    protected $table = 'ReceivingItem';
    protected $primaryKey = 'ReceivingItemID';
    public $timestamps = false;

    protected $fillable = [
        'ReceivingID',
        'ItemID',
        'BatchID',
        'QuantityReceived',
        'ExpiryDate',
        'UnitCost',
        'DateReceived',
        'WarehouseID',
    ];

    protected $casts = [
        'ExpiryDate' => 'date',
        'DateReceived' => 'date',
        'UnitCost' => 'decimal:2',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class, 'ReceivingID');
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Inventory\Batch::class, 'BatchID');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\Inventory\Item::class, 'ItemID');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Procurement\Warehouse::class, 'WarehouseID');
    }
}
