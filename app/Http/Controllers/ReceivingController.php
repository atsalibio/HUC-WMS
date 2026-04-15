<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\ReceivingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivingController extends Controller
{
    protected $receivingService;

    public function __construct(ReceivingService $receivingService)
    {
        $this->receivingService = $receivingService;
    }

    public function receive(Request $request)
    {
        $data = $request->validate([
            'poId' => 'required',
            'warehouseId' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantityReceived' => 'required|numeric|min:0.01',
            'items.*.unitCost' => 'sometimes|numeric|min:0',
            'items.*.expiryDate' => 'nullable|date',
            'items.*.lotNumber' => 'required|string',
            'items.*.batchId' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $result = $this->receivingService->receiveItems($data['poId'], $data['items'], $user->UserID, $data['warehouseId']);
            return response()->json(['success' => true, 'receiving' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function discard(Request $request)
    {
        $data = $request->validate([
            'poId' => 'required',
        ]);

        $user = Auth::user();

        try {
            $result = $this->receivingService->discardShipment($data['poId'], $user->UserID);
            return response()->json(['success' => true, 'receiving' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
