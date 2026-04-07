<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'Warehouse';
    protected $primaryKey = 'WarehouseID';
    public $timestamps = false; // Legacy schema does not use standard Laravel timestamps

    protected $fillable = [
        'WarehouseName',
        'Location',
        'WarehouseType',
    ];
}
