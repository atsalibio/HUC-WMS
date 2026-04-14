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
        'BatchID',
        'QuantityReceived',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class, 'ReceivingID');
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Inventory\Batch::class, 'BatchID');
    }
}
