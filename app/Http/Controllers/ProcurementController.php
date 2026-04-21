<?php

namespace App\Http\Controllers;

use App\Services\ProcurementService;
use App\Services\SupplierService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    protected $procurementService;
    protected $supplierService;

    public function __construct(ProcurementService $procurementService, SupplierService $supplierService)
    {
        $this->procurementService = $procurementService;
        $this->supplierService = $supplierService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|integer',
            'supplier_name' => 'nullable|string',
            'supplier_address' => 'nullable|string',
            'health_center_id' => 'nullable|integer',
            'contract_number' => 'nullable|string',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'contract_amount' => 'nullable|numeric',
            'document_type' => 'nullable|string',
            'photo' => 'nullable|file|image|max:5120',
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unitCost' => 'sometimes|numeric',
            'items.*.expiryDate' => 'nullable|sometimes|date',
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photo->move(public_path('assets/img/uploads/procurement'), $filename);
                $data['photo_path'] = 'assets/img/uploads/procurement/' . $filename;
            }

            if ($data['supplier_id'] === null && $data['supplier_name']) {
                $supplier = $this->supplierService->createSupplier([
                    'Name' => $data['supplier_name'],
                    'Address' => $data['supplier_address'] ?? null,
                ]);
                $data['supplier_id'] = $supplier->SupplierID;
            } elseif ($data['supplier_id'] === null) {
                return response()->json(['success' => false, 'message' => 'Either supplier ID or name must be provided.'], 400);
            }

            $po = $this->procurementService->createProcurementOrder($data, $user->UserID);
            return response()->json(['success' => true, 'procurementOrder' => $po]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Approved,Rejected,Pending,Completed',
        ]);

        $user = Auth::user();

        try {
            $po = $this->procurementService->updatePOStatus($id, $data['status'], $user->UserID);
            return response()->json(['success' => true, 'procurementOrder' => $po]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update PO status: ' . $e->getMessage()], 500);
        }
    }
}
