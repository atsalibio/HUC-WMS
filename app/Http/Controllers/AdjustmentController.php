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
use App\Models\Inventory\Adjustment\InventoryCorrection;
use App\Models\Inventory\Adjustment\RecallOrder;
use App\Models\Inventory\Adjustment\RecallFulfillment;
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

        $corrections = InventoryCorrection::with(['batch.item', 'user', 'healthCenter', 'warehouse'])
            ->when(Auth::user()->HealthCenterID, function($query) {
                return $query->where('HealthCenterID', Auth::user()->HealthCenterID);
            })
            ->when(!Auth::user()->HealthCenterID, function($query) {
                return $query->where('WarehouseID', 1);
            })
            ->orderBy('CorrectionDate', 'desc')
            ->get();

        $recalls = RecallOrder::with(['batch.item', 'user'])
            ->orderBy('RecallDate', 'desc')
            ->limit(10)
            ->get();

        return view('pages.adjustments', [
            'items' => $items,
            'inventory' => $inventory,
            'requisitions' => $requisitions,
            'history' => $history,
            'disposals' => $disposals,
            'returns' => $returns,
            'corrections' => $corrections,
            'recalls' => $recalls,
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

    public function updateDisposalStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed',
        ]);

        if (Auth::user()->Role !== 'Head Pharmacist') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Head Pharmacists can approve/reject disposals.'], 403);
        }

        $disposal = InventoryDisposal::findOrFail($id);
        $disposal->StatusType = $request->status;
        $disposal->save();

        return response()->json(['success' => true]);
    }

    public function updateReturnStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed',
        ]);

        if (Auth::user()->Role !== 'Head Pharmacist') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Head Pharmacists can approve/reject returns.'], 403);
        }

        $return = InventoryReturn::findOrFail($id);
        $return->StatusType = $request->status;
        $return->save();

        return response()->json(['success' => true]);
    }

    public function updateCorrectionStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Completed',
        ]);

        if (Auth::user()->Role !== 'Head Pharmacist') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Head Pharmacists can approve/reject corrections.'], 403);
        }

        $correction = InventoryCorrection::findOrFail($id);
        $correction->StatusType = $request->status;
        $correction->save();

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
            'batchId' => ['required', function ($attribute, $value, $fail) {
                $existsInCentral = Batch::where('BatchID', $value)->exists();
                $existsInHC = HCInventoryBatch::where('HCBatchID', $value)->exists();

                if (! $existsInCentral && ! $existsInHC) {
                    $fail('The selected batch ID is invalid.');
                }
            }],
            'quantity' => 'required|numeric',
            'reason' => 'required|string',
        ]);

        return DB::transaction(function() use ($request) {
            $user = Auth::user();
            $hcBatch = HCInventoryBatch::find($request->batchId);
            $batch = $hcBatch ? Batch::find($hcBatch->BatchID) : Batch::find($request->batchId);

            if (! $batch) {
                return response()->json(['success' => false, 'message' => 'Batch not found.']);
            }

            if ($batch->IsLocked) {
                return response()->json(['success' => false, 'message' => 'Batch is locked and cannot be corrected.']);
            }

            $quantityBefore = $hcBatch ? $hcBatch->QuantityOnHand : $batch->QuantityOnHand;

            $correctionData = [
                'UserID' => Auth::id(),
                'BatchID' => $batch->BatchID,
                'ItemID' => $hcBatch ? $hcBatch->ItemID : $batch->ItemID,
                'QuantityBefore' => $quantityBefore,
                'QuantityCorrected' => $request->quantity,
                'Reason' => $request->reason,
                'StatusType' => 'Pending',
                'CorrectionDate' => now(),
            ];

            if ($user->HealthCenterID) {
                $correctionData['HealthCenterID'] = $user->HealthCenterID;
                $correctionData['WarehouseID'] = null;
            } else {
                $correctionData['WarehouseID'] = 1;
                $correctionData['HealthCenterID'] = null;
            }

            InventoryCorrection::create($correctionData);

            return response()->json(['success' => true]);
        });
    }

    public function getHealthCentersForBatch($batchId)
    {
        $healthCenters = \App\Models\HealthCenter\HCInventoryBatch::with('healthCenter')
            ->where('BatchID', $batchId)
            ->where('QuantityOnHand', '>', 0)
            ->get()
            ->map(function($hcBatch) {
                return [
                    'HCBatchID' => $hcBatch->HCBatchID,
                    'HealthCenterID' => $hcBatch->HealthCenterID,
                    'Name' => $hcBatch->healthCenter->Name,
                    'QuantityOnHand' => $hcBatch->QuantityOnHand,
                ];
            });

        return response()->json(['success' => true, 'healthCenters' => $healthCenters]);
    }

    public function storeRecall(Request $request)
    {
        $request->validate([
            'batchId' => 'required|exists:CentralInventoryBatch,BatchID',
            'reason' => 'required|string',
            'selectedHCBatches' => 'required|array',
            'selectedHCBatches.*' => 'exists:HCInventoryBatch,HCBatchID',
        ]);

        // Check if user is Head Pharmacist
        if (Auth::user()->Role !== 'Head Pharmacist') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only Head Pharmacists can create recall orders.']);
        }

        return DB::transaction(function() use ($request) {
            $batch = \App\Models\Inventory\Batch::find($request->batchId);
            $totalQuantity = 0;

            // Calculate total quantity being recalled
            foreach ($request->selectedHCBatches as $hcBatchId) {
                $hcBatch = \App\Models\HealthCenter\HCInventoryBatch::find($hcBatchId);
                $totalQuantity += $hcBatch->QuantityOnHand;
            }

            // Create recall order
            $recallOrder = RecallOrder::create([
                'UserID' => Auth::id(),
                'BatchID' => $batch->BatchID,
                'ItemID' => $batch->ItemID,
                'Reason' => $request->reason,
                'QuantityOnRecall' => $totalQuantity,
                'StatusType' => 'Dispatched',
                'RecallDate' => now(),
            ]);

            return response()->json(['success' => true, 'recallOrderId' => $recallOrder->RecallOrderID]);
        });
    }

    public function storeRecallFulfillment(Request $request)
    {
        $request->validate([
            'recallOrderId' => 'required|exists:RecallOrder,RecallOrderID',
            'hcBatchId' => 'required|exists:HCInventoryBatch,HCBatchID',
            'quantityFulfilled' => 'required|numeric|min:1',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();
        if (!$user->HealthCenterID) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only health center staff can fulfill recalls.']);
        }

        $hcBatch = HCInventoryBatch::find($request->hcBatchId);
        if (!$hcBatch || $hcBatch->HealthCenterID != $user->HealthCenterID) {
            return response()->json(['success' => false, 'message' => 'Selected batch does not belong to your health center.']);
        }

        $recallOrder = RecallOrder::find($request->recallOrderId);
        if (!$recallOrder || $recallOrder->BatchID != $hcBatch->BatchID) {
            return response()->json(['success' => false, 'message' => 'Selected recall order does not match the inventory batch.']);
        }

        if ($request->quantityFulfilled > $hcBatch->QuantityOnHand) {
            return response()->json(['success' => false, 'message' => 'Fulfilled quantity cannot exceed current batch quantity on hand.']);
        }

        $evidencePath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('assets/img/uploads/adjustments'), $filename);
            $evidencePath = 'assets/img/uploads/adjustments/' . $filename;
        }

        RecallFulfillment::create([
            'RecallOrderID' => $recallOrder->RecallOrderID,
            'HCID' => $user->HealthCenterID,
            'HCBatchID' => $hcBatch->HCBatchID,
            'BatchID' => $hcBatch->BatchID,
            'ItemID' => $hcBatch->ItemID,
            'QuantityFulfilled' => $request->quantityFulfilled,
            'EvidencePath' => $evidencePath,
            'StatusType' => 'Pending',
            'CreatedAt' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
