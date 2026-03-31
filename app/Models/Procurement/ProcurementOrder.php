<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class ProcurementOrder extends Model
{
    protected $table = 'ProcurementOrder';
    protected $primaryKey = 'POID';
    public $timestamps = false; // Legacy schema uses custom CreatedAt/UpdatedAt in some places, but simplified for now

    protected $fillable = [
        'UserID',
        'SupplierID',
        'SupplierName',
        'SupplierAddress',
        'HealthCenterID',
        'ContractID',
        'PONumber',
        'PODate',
        'StatusType',
        'DocumentType',
    ];

    protected $casts = [
        'PODate' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ProcurementOrderItem::class, 'POID');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\System\User::class, 'UserID');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID');
    }

    public function healthCenter()
    {
        return $this->belongsTo(\App\Models\System\HealthCenter::class, 'HealthCenterID');
    }
}
