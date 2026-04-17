<?php

namespace App\Models\Procurement;

use App\Models\Inventory\Item;
use Illuminate\Database\Eloquent\Model;

class ProcurementOrderItem extends Model
{
    protected $table = 'ProcurementOrderItem';
    protected $primaryKey = 'POItemID';
    public $timestamps = false;

    protected $fillable = [
        'POID',
        'ItemID',
        'BatchID',
        'LotNumber',
        'QuantityOrdered',
        'UnitCost',
        'ExpiryDate',
    ];

    public function procurementOrder()
    {
        return $this->belongsTo(ProcurementOrder::class, 'POID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID');
    }
}
