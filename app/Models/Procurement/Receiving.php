<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $table = 'Receiving';
    protected $primaryKey = 'ReceivingID';
    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'POID',
        'ReceivedDate',
    ];

    public function items()
    {
        return $this->hasMany(ReceivingItem::class, 'ReceivingID');
    }

    public function procurementOrder()
    {
        return $this->belongsTo(ProcurementOrder::class, 'POID');
    }
}
