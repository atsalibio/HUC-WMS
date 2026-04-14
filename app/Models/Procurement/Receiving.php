<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $table = 'Receiving';
    protected $primaryKey = 'ReceivingID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'POID',
        'ReceivedDate',
    ];

    protected $casts = [
        'ReceivedDate' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ReceivingItem::class, 'ReceivingID');
    }

    public function procurementOrder()
    {
        return $this->belongsTo(ProcurementOrder::class, 'POID');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\System\User::class, 'UserID');
    }
}
