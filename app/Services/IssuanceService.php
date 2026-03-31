<?php

namespace App\Services;

use App\Models\Issuance\Issuance;
use App\Models\Issuance\IssuanceItem;
use App\Models\Inventory\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IssuanceService
{
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

                    $centralBatch = Batch::find($batchId);
                    if (!$centralBatch || $centralBatch->QuantityOnHand < $qtyToIssue) {
                        throw new \Exception("Insufficient stock for Batch ID {$batchId}");
                    }

                    $centralBatch->decrement('QuantityOnHand', $qtyToIssue);
                    $centralBatch->increment('QuantityReleased', $qtyToIssue);

                    // ensure HC inventory on local center
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
                                'DateReceivedAtHC' => Carbon::now(), // using the legacy naming if preferred, otherwise model handles timestamps
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

            return $issuance;
        });
    }
}
