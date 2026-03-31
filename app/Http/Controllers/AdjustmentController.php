<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory\Adjustment;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Requisition\Requisition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdjustmentController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $inventory = Batch::where('QuantityOnHand', '>', 0)->get();
        $requisitions = Requisition::with(['healthCenter', 'user', 'items.item'])
            ->whereIn('StatusType', ['Completed', 'Issued'])
            ->orderBy('RequestDate', 'desc')
            ->get();
            
        $history = Adjustment::with(['batch.item', 'user', 'requisition'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.adjustments', compact('items', 'inventory', 'requisitions', 'history'));
    }

    public function storeDisposal(Request $request)
    {
        $request->validate([
            'batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'quantity' => 'required|numeric|min:1',
            'reason' => 'required|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        return DB::transaction(function() use ($request) {
            $batch = Batch::lockForUpdate()->find($request->batchId);
            
            if ($batch->QuantityOnHand < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock in batch.']);
            }

            $evidencePath = null;
            if ($request->hasFile('photo')) {
                $evidencePath = $request->file('photo')->store('adjustments/disposals', 'public');
            }

            Adjustment::create([
                'BatchID' => $batch->BatchID,
                'AdjustmentType' => 'Disposal',
                'QuantityAdjusted' => $request->quantity * -1,
                'Reason' => $request->reason,
                'Remarks' => $request->remarks,
                'EvidencePath' => $evidencePath,
                'AdjustedBy' => Auth::id()
            ]);

            $batch->decrement('QuantityOnHand', $request->quantity);

            return response()->json(['success' => true]);
        });
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'requisitionId' => 'required|exists:Requisition,RequisitionID',
            'reason' => 'required|string',
            'items' => 'required|array',
            'items.*.batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function() use ($request) {
            foreach ($request->items as $item) {
                $batch = Batch::lockForUpdate()->find($item['batchId']);
                
                Adjustment::create([
                    'BatchID' => $batch->BatchID,
                    'RequisitionID' => $request->requisitionId,
                    'AdjustmentType' => 'Return',
                    'QuantityAdjusted' => $item['quantity'],
                    'Reason' => $request->reason,
                    'AdjustedBy' => Auth::id()
                ]);

                $batch->increment('QuantityOnHand', $item['quantity']);
            }

            return response()->json(['success' => true]);
        });
    }
    public function storeCorrection(Request $request)
    {
        $request->validate([
            'batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'quantity' => 'required|numeric', // can be positive or negative
            'reason' => 'required|string',
        ]);

        return DB::transaction(function() use ($request) {
            $batch = Batch::lockForUpdate()->find($request->batchId);
            
            // If decreasing, check for sufficient stock
            if ($request->quantity < 0 && $batch->QuantityOnHand < abs($request->quantity)) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for this correction.']);
            }

            Adjustment::create([
                'BatchID' => $batch->BatchID,
                'AdjustmentType' => 'Correction',
                'QuantityAdjusted' => $request->quantity,
                'Reason' => $request->reason,
                'Remarks' => $request->remarks,
                'AdjustedBy' => Auth::id()
            ]);

            if ($request->quantity > 0) {
                $batch->increment('QuantityOnHand', abs($request->quantity));
            } else {
                $batch->decrement('QuantityOnHand', abs($request->quantity));
            }

            return response()->json(['success' => true]);
        });
    }
}
