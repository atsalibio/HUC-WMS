<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'Contract';
    protected $primaryKey = 'ContractID';
    public $timestamps = false;

    protected $fillable = [
        'SupplierID',
        'ContractNumber',
        'StartDate',
        'EndDate',
        'ContractAmount',
        'StatusType',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID');
    }
}
