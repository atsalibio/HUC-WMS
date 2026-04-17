<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\IssuanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssuanceController extends Controller
{
    protected $issuanceService;

    public function __construct(IssuanceService $issuanceService)
    {
        $this->issuanceService = $issuanceService;
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'requisitionId' => 'required|integer',
            'allocationPlan' => 'required|array',
        ]);

        $user = Auth::user();

        try {
            $result = $this->issuanceService->processIssuance($data['requisitionId'], $data['allocationPlan'], $user->UserID);
            return response()->json(['success' => true, 'issuance' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Returns available batches for a specific item in FEFO order.
     * Used by the frontend batch-selection dropdown.
     */
    public function getFefoAllocation(Request $request)
    {
        $data = $request->validate([
            'item_id'      => 'required|integer',
            'qty_needed'   => 'sometimes|integer|min:1',
            'warehouse_id' => 'sometimes|integer',
        ]);

        $batches = $this->issuanceService->getAvailableBatchesFEFO(
            $data['item_id'],
            $data['warehouse_id'] ?? null
        );

        return response()->json([
            'batches' => $batches->map(fn($b) => [
                'BatchID'       => $b->BatchID,
                'LotNumber'     => $b->LotNumber,
                'ExpiryDate'    => $b->ExpiryDate,
                'QuantityOnHand' => $b->QuantityOnHand,
            ]),
        ]);
    }
}
