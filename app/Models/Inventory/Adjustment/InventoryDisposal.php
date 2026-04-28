<?php

namespace App\Models\Inventory\Adjustment;

use Illuminate\Database\Eloquent\Model;
use App\Models\System\User;
use App\Models\System\HealthCenter;
use App\Models\Procurement\Warehouse;
use App\Models\Inventory\Item;
use App\Models\Requisition\Requisition;

class InventoryDisposal extends Model
{
    protected $table = 'InventoryDisposal';
    protected $primaryKey = 'DisposalID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'WarehouseID',
        'BatchID',
        'ItemID',
        'QuantityDisposed',
        'DisposalType',
        'EvidencePath',
        'StatusType',
        'DisposalDate',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'WarehouseID');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID', 'ItemID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
