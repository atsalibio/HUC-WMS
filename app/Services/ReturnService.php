<?php

namespace App\Services;

use App\Models\Inventory\Adjustment\InventoryReturn;
use App\Models\Inventory\Adjustment\InventoryReturnItem;
use App\Models\Inventory\Batch;
use App\Models\HealthCenter\HCInventoryBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class ReturnService
{
    public function processReturn(int $returnId)
    {
        $inventoryReturn = InventoryReturnItem::where('ReturnID', $returnId)->with('batch', 'hcBatch')->get();

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

            return response()->json(['success' => true]);
        });
    }

    public function updateStatus(int $returnId, string $status)
    {
        $inventoryReturn = InventoryReturn::findOrFail($returnId);
        $inventoryReturn->StatusType = $status;
        $inventoryReturn->save();

        return response()->json(['success' => true]);
    }

    public function storeReturn($returnRequest)
    {
        try {

            InventoryReturn::create([
                'UserID' => $returnRequest['userId'],
                'HCID' => $returnRequest['hcId'],
                'HCBatchID' => $returnRequest['hcBatchId'],
                'BatchID' => $returnRequest['batchId'],
                'ItemID' => $returnRequest['itemId'],
                'QuantityReturned' => $returnRequest['quantity'],
                'WarehouseID' => $returnRequest['warehouseId'],
                'Reason' => $returnRequest['reason'],
                'EvidencePath' => $returnRequest['evidencePath'] ?? null,
                'ReturnDate' => now(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
