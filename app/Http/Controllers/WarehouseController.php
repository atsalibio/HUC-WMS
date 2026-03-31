<?php

namespace App\Http\Controllers;

use App\Services\WarehouseService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $warehouses = $this->warehouseService->getAllWarehouses();
        return response()->json($warehouses);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouseName' => 'required|string|max:200',
            'location' => 'nullable|string',
            'warehouseType' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();

        try {
            $warehouse = $this->warehouseService->createWarehouse($data, $user->UserID);
            return response()->json(['success' => true, 'warehouse' => $warehouse]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'warehouseName' => 'required|string|max:200',
            'location' => 'nullable|string',
            'warehouseType' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();

        try {
            $warehouse = $this->warehouseService->updateWarehouse($id, $data, $user->UserID);
            return response()->json(['success' => true, 'warehouse' => $warehouse]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
