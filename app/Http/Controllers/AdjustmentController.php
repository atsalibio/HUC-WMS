<?php

namespace App\Http\Controllers;

use App\Models\HealthCenter\HCInventoryBatch;
use Illuminate\Http\Request;
use App\Models\Inventory\Adjustment\InventoryReturn;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Inventory\Adjustment;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\Adjustment\InventoryDisposal;
use App\Models\Inventory\Adjustment\InventoryHCCorrection;
use App\Models\Inventory\Adjustment\InventoryWarehouseCorrection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdjustmentController extends Controller
{
    public function index()
    {
        $requisitions = Requisition::with(['healthCenter', 'user', 'items.item', 'issuances.items.batch.item'])
            ->whereIn('StatusType', ['Completed', 'Issued'])
            ->orderBy('RequestDate', 'desc')
            ->get();

        if(!Auth::user()->HealthCenterID){
            $inventory = Batch::where('QuantityOnHand', '>', 0)->where('IsLocked', false)->get();
        }
        else{
            $inventory = HCInventoryBatch::with(['item'])
                ->where('QuantityOnHand', '>', 0)
                ->whereHas('healthCenter', function($query) {
                    $query->where('HealthCenterID', Auth::user()->HealthCenterID);
                })
                ->get();
        }

        $items = Item::whereIn('ItemID', $inventory->select('ItemID'))->get();

        $history = Adjustment::with(['batch.item', 'user', 'batch'])
            ->orderBy('AdjustmentDate', 'desc')
            ->get();

        $disposals = InventoryDisposal::with(['warehouse', 'user', 'item'])
            ->where('StatusType', '!=', 'Completed')
            ->where('StatusType', '!=', 'Rejected')
            ->orderBy('DisposalDate', 'desc')
            ->limit(6)
            ->get();

        $returns = InventoryReturn::with(['batch.item', 'user', 'healthCenter'])
            ->where('HCID', Auth::user()->HealthCenterID)
            ->where('StatusType', '!=', 'Completed')
            ->where('StatusType', '!=', 'Rejected')
            ->orderBy('ReturnDate', 'desc')
            ->get();

        $corrections = Adjustment::with(['batch.item', 'user'])
            ->where('AdjustmentType', 'Correction')
            ->orderBy('AdjustmentDate', 'desc')
            ->limit(6)
            ->get();

        return view('pages.adjustments', [
            'items' => $items,
            'inventory' => $inventory,
            'requisitions' => $requisitions,
            'history' => $history,
            'disposals' => $disposals,
            'returns' => $returns,
            'corrections' => $corrections,
            'currentPage' => 'adjustments'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed',
        ]);

        $adjustment = Adjustment::findOrFail($id);

        $adjustment->StatusType = $request->status;
        $adjustment->save();

        return response()->json(['success' => true]);
    }

    public function storeDisposal(Request $request)
    {
        $request->validate([
            'batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'quantity' => 'required|numeric|min:1',
            'disposalType' => 'required|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        return DB::transaction(function() use ($request) {
            $batch = Batch::find($request->batchId);

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

            InventoryDisposal::create([
                'BatchID' => $batch->BatchID,
                'ItemID' => $batch->ItemID,
                'WarehouseID' => $batch->WarehouseID,
                'UserID' => Auth::id(),
                'QuantityDisposed' => $request->quantity,
                'DisposalType' => $request->disposalType,
                'EvidencePath' => $evidencePath,
                'DisposalDate' => now(),
            ]);

            return response()->json(['success' => true]);
        });
    }

    public function storeReturn(Request $request)
    {
        $data = $request->validate([
            'hcBatchId' => 'required|exists:HCInventoryBatch,HCBatchID',
            'reason' => 'required|string',
            'photo' => 'nullable|image|max:5120',
            'quantity' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        try {

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $photo->move(public_path('assets/img/uploads/procurement'), $filename);
                $data['photo_path'] = 'assets/img/uploads/procurement/' . $filename;
            }

            $itemReturn = HCInventoryBatch::find($data['hcBatchId']);

            InventoryReturn::create([
                'UserID' => $user->HealthCenterID,
                'HCID' => $user->HealthCenterID,
                'HCBatchID' => $data['hcBatchId'],
                'BatchID' => $itemReturn->BatchID,
                'ItemID' => $itemReturn->ItemID,
                'QuantityReturned' => $data['quantity'],
                'WarehouseID' => 1,
                'Reason' => $data['reason'],
                'EvidencePath' => $data['photo_path'] ?? null,
                'ReturnDate' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function processReturn(int $returnId)
    {
        //$inventoryReturn = InventoryReturnItem::where('ReturnID', $returnId)->with('batch', 'hcBatch')->get();

        return DB::transaction(function() use ($inventoryReturn) {
            foreach ($inventoryReturn->items as $returnItem) {
                $batch = Batch::find($returnItem->BatchID);
                $hcBatch = HCInventoryBatch::find($returnItem->HCBatchID);

                if ($batch->IsLocked) {
                    throw new \Exception("Batch {$batch->LotNumber} is locked and cannot be returned to.");
                }

                $batch->QuantityOnHand += $returnItem->QuantityReturned;
                $batch->save();

                $hcBatch->QuantityOnHand -= $returnItem->QuantityReturned;
                $hcBatch->save();
            }

            $inventoryReturn->StatusType = 'Completed';
            $inventoryReturn->save();

            return response()->json(['success' => true]);
        });
    }

    public function storeCorrection(Request $request)
    {
        $request->validate([
            'batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'beforeQuantity' => 'required|numeric|min:0',
            'quantity' => 'required|numeric',
            'reason' => 'required|string',
        ]);

        return DB::transaction(function() use ($request) {
            $batch = Batch::find($request->batchId);

            if ($batch->IsLocked) {
                return response()->json(['success' => false, 'message' => 'Batch is locked and cannot be corrected.']);
            }

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

            return response()->json(['success' => true]);
        });
    }
}
