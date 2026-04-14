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
        $inventory = Batch::where('QuantityOnHand', '>', 0)->where('IsLocked', false)->get();
        $requisitions = Requisition::with(['healthCenter', 'user', 'items.item', 'issuances.items.batch.item'])
            ->whereIn('StatusType', ['Completed', 'Issued'])
            ->orderBy('RequestDate', 'desc')
            ->get();
            
        $history = Adjustment::with(['batch.item', 'user'])
            ->orderBy('AdjustmentDate', 'desc')
            ->get();

        return view('pages.adjustments', [
            'items' => $items,
            'inventory' => $inventory,
            'requisitions' => $requisitions,
            'history' => $history,
            'currentPage' => 'adjustments'
        ]);
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
            
            if ($batch->IsLocked) {
                return response()->json(['success' => false, 'message' => 'Batch is locked and cannot be adjusted.']);
            }

            if ($batch->QuantityOnHand < $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock in batch.']);
            }

            $evidencePath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photo->move(public_path('assets/img/uploads/adjustments'), $filename);
                $evidencePath = 'assets/img/uploads/adjustments/' . $filename;
            }

            Adjustment::create([
                'BatchID' => $batch->BatchID,
                'UserID' => Auth::id(),
                'AdjustmentType' => 'Disposal',
                'AdjustmentQuantity' => $request->quantity * -1,
                'Reason' => $request->reason,
                'EvidencePath' => $evidencePath,
                'AdjustmentDate' => now(),
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
                
                if ($batch->IsLocked) {
                    throw new \Exception("Batch {$batch->LotNumber} is locked and cannot be returned to.");
                }
                
                Adjustment::create([
                    'BatchID' => $batch->BatchID,
                    'UserID' => Auth::id(),
                    'AdjustmentType' => 'Return',
                    'AdjustmentQuantity' => $item['quantity'],
                    'Reason' => $request->reason,
                    'RequisitionID' => $request->requisitionId,
                    'AdjustmentDate' => now(),
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
            
            if ($batch->IsLocked) {
                return response()->json(['success' => false, 'message' => 'Batch is locked and cannot be corrected.']);
            }

            // If decreasing, check for sufficient stock
            if ($request->quantity < 0 && $batch->QuantityOnHand < abs($request->quantity)) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for this correction.']);
            }

            Adjustment::create([
                'BatchID' => $batch->BatchID,
                'UserID' => Auth::id(),
                'AdjustmentType' => 'Correction',
                'AdjustmentQuantity' => $request->quantity,
                'Reason' => $request->reason,
                'AdjustmentDate' => now(),
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
