<?php

namespace App\Services;

use App\Models\Procurement\Receiving;
use App\Models\Procurement\ReceivingItem;
use App\Models\Inventory\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;
use App\Models\System\TransactionLog;

class ReceivingService
{
    public function receiveItems($poId, array $items, int $userId, int $warehouseId = 1)
    {
        return DB::transaction(function () use ($poId, $items, $userId, $warehouseId) {
            if (empty($poId)) {
                throw new \Exception('PO ID is required');
            }

            // Mark PO as completed (For now, full receipts only)
            $po = DB::table('ProcurementOrder')->where('POID', $poId)->first();
            DB::table('ProcurementOrder')->where('POID', $poId)->update(['StatusType' => 'Completed']);

            $receiving = Receiving::create([
                'POID' => $poId,
                'ReceivedDate' => Carbon::now(),
                'UserID' => $userId,
            ]);

            foreach ($items as $item) {
                $qtyReceived = (float)($item['quantityReceived'] ?? 0);
                if ($qtyReceived <= 0) continue;

                $expiryDate = null;
                if (!empty($item['expiryDate'])) {
                    try {
                        $expiryDate = Carbon::parse($item['expiryDate'])->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                // 1. Fetch LotNumber from ProcurementOrderItem if it exists
                $poItem = DB::table('ProcurementOrderItem')
                    ->where('POID', $poId)
                    ->where('ItemID', $item['itemId'])
                    ->first();

                $lotNumber = $item['lotNumber'] ?? ($poItem->LotNumber ?? ('LOT-' . strtoupper(bin2hex(random_bytes(3)))));

                // 2. Create a batch in central inventory
                $batch = Batch::create([
                    'ItemID' => $item['itemId'],
                    'LotNumber' => $lotNumber,
                    'BatchNumber' => $item['batchId'] ?? ($poItem->BatchID ?? null),
                    'WarehouseID' => $warehouseId,
                    'ExpiryDate' => $expiryDate,
                    'QuantityOnHand' => $qtyReceived,
                    'QuantityReleased' => 0,
                    'IsLocked' => false, // Set to false by default so it can be discarded/adjusted if damaged
                    'UnitCost' => (float)($item['unitCost'] ?? 0),
                    'DateReceived' => Carbon::now(),
                ]);

                // 3. Link batch to the receiving record
                ReceivingItem::create([
                    'ReceivingID' => $receiving->ReceivingID,
                    'BatchID' => $batch->BatchID,
                    'QuantityReceived' => $qtyReceived,
                ]);
            }

            // 通知 Trigger: Notify Admin and Accounting
            if ($po) {
                NotificationController::create(
                    "Delivery Processed",
                    "Items for Order {$po->PONumber} from {$po->SupplierName} have been processed.",
                    "/receiving",
                    "Administrator"
                );
                NotificationController::create(
                    "Valuation Update Required",
                    "New stock from {$po->SupplierName} is ready for financial audit.",
                    "/reports",
                    "Accounting Office User"
                );

                // Add to System Ledger
                TransactionLog::create([
                    'UserID' => $userId,
                    'ReferenceType' => 'Delivery Processed',
                    'ReferenceID' => $po->PONumber,
                    'ActionType' => 'Receiving',
                    'ActionDetails' => "Processed delivery for PO '{$po->PONumber}' from supplier '{$po->SupplierName}'.",
                    'ActionDate' => Carbon::now()
                ]);
            }

            return $receiving;
        });
    }
}
