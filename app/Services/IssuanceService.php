<?php

namespace App\Services;

use App\Models\Issuance\Issuance;
use App\Models\Issuance\IssuanceItem;
use App\Models\System\TransactionLog;
use App\Models\System\HealthCenter;
use App\Models\Inventory\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IssuanceService
{
    /**
     * Get available batches for an item sorted by FEFO (earliest expiry first).
     * Filters out expired and out-of-stock batches.
     */
    public function getAvailableBatchesFEFO(int $itemId, ?int $warehouseId = null): \Illuminate\Support\Collection
    {
        $query = Batch::where('ItemID', $itemId)
            ->where('QuantityOnHand', '>', 0)
            ->where('IsLocked', false)
            ->where(function ($q) {
                $q->whereNull('ExpiryDate')
                  ->orWhere('ExpiryDate', '>=', Carbon::today());
            });

        if ($warehouseId) {
            $query->where('WarehouseID', $warehouseId);
        }

        return $query->orderBy('ExpiryDate', 'asc')->get();
    }

    /**
     * Process issuance: accepts allocationPlan or auto-allocates via FEFO.
     */
    public function processIssuance(int $requisitionId, array $allocationPlan, int $userId)
    {
        return DB::transaction(function () use ($requisitionId, $allocationPlan, $userId) {
            $requisition = \App\Models\Requisition\Requisition::findOrFail($requisitionId);

            foreach ($allocationPlan as $planItem) {
                if (empty($planItem['allocated']) || !is_array($planItem['allocated'])) {
                    continue;
                }

                foreach ($planItem['allocated'] as $allocatedBatch) {
                    $batchId = $allocatedBatch['BatchID'] ?? null;
                    $qtyToIssue = (int)($allocatedBatch['Quantity'] ?? 0);
                    if (!$batchId || $qtyToIssue <= 0) {
                        continue;
                    }

                    // Validate stock availability
                    $centralBatch = Batch::find($batchId);
                    if (!$centralBatch) {
                        throw new \Exception("Batch ID {$batchId} not found.");
                    }
                    if ($centralBatch->QuantityOnHand < $qtyToIssue) {
                        throw new \Exception("Insufficient stock for Batch ID {$batchId}. Available: {$centralBatch->QuantityOnHand}, Requested: {$qtyToIssue}");
                    }
                    // Block issuing locked batches
                    if ($centralBatch->IsLocked) {
                        throw new \Exception("Batch ID {$batchId} (Lot: {$centralBatch->LotNumber}) is locked and cannot be transferred/issued.");
                    }
                    // Block issuing expired batches
                    if ($centralBatch->ExpiryDate && Carbon::parse($centralBatch->ExpiryDate)->isPast()) {
                        throw new \Exception("Batch ID {$batchId} is expired and cannot be issued.");
                    }

                    $centralBatch->decrement('QuantityOnHand', $qtyToIssue);
                    $centralBatch->increment('QuantityReleased', $qtyToIssue);

                    // Update HC inventory
                    $hcId = $requisition->HealthCenterID;
                    if ($hcId) {
                        \App\Models\HealthCenter\HCInventoryBatch::updateOrCreate(
                            [
                                'HealthCenterID' => $hcId,
                                'ItemID' => $centralBatch->ItemID,
                                'BatchID' => $batchId,
                            ],
                            [
                                'ExpiryDate' => $centralBatch->ExpiryDate,
                                'QuantityOnHand' => DB::raw("QuantityOnHand + {$qtyToIssue}"),
                                'UnitCost' => $centralBatch->UnitCost ?? 0,
                                'DateReceivedAtHC' => Carbon::now(),
                            ]
                        );
                    }
                }
            }

            $issuance = Issuance::create([
                'RequisitionID' => $requisitionId,
                'UserID' => $userId,
                'IssueDate' => Carbon::now(),
                'StatusType' => 'Issued',
            ]);

            foreach ($allocationPlan as $planItem) {
                if (empty($planItem['allocated']) || !is_array($planItem['allocated'])) {
                    continue;
                }

                foreach ($planItem['allocated'] as $allocatedBatch) {
                    $batchId = $allocatedBatch['BatchID'] ?? null;
                    $qtyToIssue = (int)($allocatedBatch['Quantity'] ?? 0);
                    if (!$batchId || $qtyToIssue <= 0) {
                        continue;
                    }
                    IssuanceItem::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'BatchID' => $batchId,
                        'RequisitionItemID' => $planItem['reqItemId'] ?? null,
                        'QuantityIssued' => $qtyToIssue,
                    ]);
                }
            }

            DB::table('Requisition')->where('RequisitionID', $requisitionId)->update(['StatusType' => 'Completed']);

            $healthCenter = HealthCenter::find($requisition->HealthCenterID);

            TransactionLog::create([
                'UserID' => $userId,
                'ReferenceType' => "Requisition Issued",
                'ReferenceID' => $issuance->IssuanceID,
                'ActionType' => 'Issuance',
                'ActionDetails' => "Processed issuance for requisition '{$requisition->RequisitionNumber}' for {$healthCenter->Name}.",
                'ActionDate' => Carbon::now()
            ]);

            return $issuance;
        });
    }

    /**
     * Auto-allocate: given a requisition item + qty needed, return FEFO batch allocations.
     */
    public function autoAllocateFEFO(int $itemId, int $qtyNeeded, ?int $warehouseId = null): array
    {
        $batches = $this->getAvailableBatchesFEFO($itemId, $warehouseId);
        $allocated = [];
        $remaining = $qtyNeeded;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min($batch->QuantityOnHand, $remaining);
            $allocated[] = [
                'BatchID' => $batch->BatchID,
                'LotNumber' => $batch->LotNumber,
                'ExpiryDate' => $batch->ExpiryDate,
                'AvailableQty' => $batch->QuantityOnHand,
                'Quantity' => $take,
            ];
            $remaining -= $take;
        }

        return $allocated;
    }
}
