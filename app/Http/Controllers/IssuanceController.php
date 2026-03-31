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
}
