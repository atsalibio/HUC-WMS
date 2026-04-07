<?php

namespace App\Services;

use App\Models\Procurement\Receiving;
use App\Models\Procurement\ReceivingItem;
use App\Models\Inventory\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceivingService
{
    public function receiveItems($poId, array $items, int $userId, int $warehouseId = 1)
    {
        return DB::transaction(function () use ($poId, $items, $userId, $warehouseId) {
            if (empty($poId)) {
                throw new \Exception('PO ID is required');
            }

            // Mark PO as completed (For now, full receipts only)
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

                // 1. Create a batch in central inventory
                $batch = Batch::create([
                    'ItemID' => $item['itemId'],
                    'LotNumber' => $item['lotNumber'] ?? ('LOT-' . time()),
                    'WarehouseID' => $warehouseId,
                    'ExpiryDate' => $expiryDate,
                    'QuantityOnHand' => $qtyReceived,
                    'QuantityReleased' => 0,
                    'UnitCost' => (float)($item['unitCost'] ?? 0),
                    'DateReceived' => Carbon::now(),
                ]);

                // 2. Link batch to the receiving record
                ReceivingItem::create([
                    'ReceivingID' => $receiving->id,
                    'BatchID' => $batch->BatchID,
                    'QuantityReceived' => $qtyReceived,
                ]);
            }

            return $receiving;
        });
    }
}
