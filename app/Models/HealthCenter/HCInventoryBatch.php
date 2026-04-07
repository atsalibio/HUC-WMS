<?php

namespace App\Models\HealthCenter;

use App\Models\Inventory\Item;
use App\Models\System\HealthCenter;
use Illuminate\Database\Eloquent\Model;

class HCInventoryBatch extends Model
{
    protected $table = 'HCInventoryBatch';
    protected $primaryKey = 'HCBatchID';
    public $timestamps = false;

    protected $fillable = [
        'HealthCenterID',
        'ItemID',
        'BatchID',
        'LotNumber',
        'ExpiryDate',
        'QuantityReceived',
        'QuantityOnHand',
        'DateReceivedAtHC'
    ];

    protected $casts = [
        'ExpiryDate' => 'datetime',
        'DateReceivedAtHC' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'ItemID', 'ItemID');
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class, 'HealthCenterID', 'HealthCenterID');
    }
}
