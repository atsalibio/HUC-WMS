<?php

namespace App\Services;

use App\Models\Procurement\Warehouse;
use App\Models\System\SecurityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class WarehouseService
{
    public function createWarehouse(array $data, $userId)
    {
        $warehouse = Warehouse::create([
            'WarehouseName' => $data['warehouseName'],
            'Location' => $data['location'],
            'WarehouseType' => $data['warehouseType'],
        ]);

        SecurityLog::create([
            'UserID' => $userId,
            'ActionType' => 'Warehouse',
            'ActionDescription' => 'Created warehouse ' . $warehouse->WarehouseName,
            'IPAddress' => Request::ip(),
            'ModuleAffected' => 'Warehouse',
            'ActionDate' => Carbon::now(),
        ]);

        return $warehouse;
    }

    public function getAllWarehouses()
    {
        return Warehouse::all();
    }

    public function updateWarehouse($id, array $data, $userId)
    {
        $warehouse = Warehouse::findOrFail($id);
        
        $warehouse->WarehouseName = $data['warehouseName'];
        $warehouse->Location = $data['location'];
        $warehouse->WarehouseType = $data['warehouseType'];
        $warehouse->save();

        SecurityLog::create([
            'UserID' => $userId,
            'ActionType' => 'Warehouse',
            'ActionDescription' => 'Updated warehouse ' . $warehouse->WarehouseName,
            'IPAddress' => Request::ip(),
            'ModuleAffected' => 'Warehouse',
            'ActionDate' => Carbon::now(),
        ]);

        return $warehouse;
    }
}
