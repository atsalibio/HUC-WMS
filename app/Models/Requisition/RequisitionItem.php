<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'RequisitionItem';
    protected $primaryKey = 'RequisitionItemID';
    public $timestamps = false;

    protected $fillable = [
        'RequisitionID',
        'ItemID',
        'QuantityRequested',
        'StatusType'
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'RequisitionID');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\Inventory\Item::class, 'ItemID');
    }
}
