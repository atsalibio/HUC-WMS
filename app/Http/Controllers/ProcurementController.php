<?php

namespace App\Http\Controllers;

use App\Services\ProcurementService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    protected $procurementService;

    public function __construct(ProcurementService $procurementService)
    {
        $this->procurementService = $procurementService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|integer',
            'supplier_name' => 'nullable|string',
            'supplier_address' => 'nullable|string',
            'health_center_id' => 'nullable|integer',
            'contract_number' => 'nullable|string',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'contract_amount' => 'nullable|numeric',
            'document_type' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unitCost' => 'sometimes|numeric',
            'items.*.expiryDate' => 'sometimes|date',
        ]);

        $user = Auth::user();

        try {
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
