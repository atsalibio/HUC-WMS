<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'Item';
    protected $primaryKey = 'ItemID';
    public $timestamps = false;

    protected $fillable = [
        'ItemName',
        'Brand',
        'ItemType',
        'UnitOfMeasure',
        'DosageUnit',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class, 'ItemID', 'ItemID');
    }
}
