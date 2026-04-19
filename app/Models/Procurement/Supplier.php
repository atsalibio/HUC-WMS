<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'Supplier';
    protected $primaryKey = 'SupplierID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Address',
        'ContactInfo',
        'LastUpdated',
        'IsActive',
        'DeletedAt'
    ];
}
