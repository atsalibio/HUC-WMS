<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\RequisitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisitionController extends Controller
{
    protected $requisitionService;

    public function __construct(RequisitionService $requisitionService)
    {
        $this->requisitionService = $requisitionService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'healthCenterId' => 'nullable|integer',
            'healthCenterName' => 'nullable|string|max:255',
            'healthCenterAddress' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        if ($user->Role === 'Health Center Staff' && $data['healthCenterId'] != $user->HealthCenterID) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Health Center mismatch'], 403);
        }

        $requisition = $this->requisitionService->createCentralRequisition($data, $user->UserID);

        return response()->json(['success' => true, 'requisition' => $requisition]);
    }

    public function storeLocal(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
            'staffName' => 'required|string',
        ]);

        $user = Auth::user();
        $healthCenterId = $user->HealthCenterID;

        if (!$healthCenterId) {
            return response()->json(['success' => false, 'message' => 'No health center assigned to user'], 422);
        }

        $requisition = $this->requisitionService->createLocalRequisition($data, $healthCenterId, $user->UserID);

        return response()->json(['success' => true, 'requisition' => $requisition]);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Approved,Rejected,Pending,Completed',
            'itemStatuses' => 'nullable|array', // per-item overrides
            'remarks' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Health Center Staff cannot approve or reject
        if ($user->Role === 'Health Center Staff' && in_array($data['status'], ['Approved', 'Rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You do not have permission to approve or reject requisitions.'
            ], 403);
        }

        try {
            $requisition = $this->requisitionService->updateStatus(
                $id,
                $data['status'],
                $user->UserID,
                $data['itemStatuses'] ?? [],
                $data['remarks'] ?? null
            );
            return response()->json(['success' => true, 'requisition' => $requisition]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update requisition status'], 500);
        }
    }
}
