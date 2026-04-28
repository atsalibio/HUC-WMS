<?php

namespace App\Models\Issuance;

use Illuminate\Database\Eloquent\Model;

class IssuanceItem extends Model
{
    protected $table = 'IssuanceItem';
    protected $primaryKey = 'IssuanceItemID';
    public $timestamps = false;

    protected $fillable = [
        'IssuanceID',
        'ItemID',
        'BatchID',
        'HCBatchID',
        'RequisitionItemID',
        'QuantityIssued'
    ];

    public function issuance()
    {
        return $this->belongsTo(Issuance::class, 'IssuanceID');
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Inventory\Batch::class, 'BatchID', 'BatchID');
    }

    public function item()
    {
        return $this->hasOneThrough(
            \App\Models\Inventory\Item::class,
            \App\Models\Inventory\Batch::class,
            'BatchID', // Local key on central_inventory_batches table
            'ItemID',  // Local key on inventory_items table
            'BatchID', // Local key on issuance_items table
            'ItemID'   // Relation key on central_inventory_batches table
        );
    }
}
