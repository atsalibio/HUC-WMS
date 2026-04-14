<?php

namespace App\Services;

use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\System\HealthCenter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;

class RequisitionService
{
    public function createCentralRequisition(array $payload, int $userId)
    {
        return DB::transaction(function () use ($payload, $userId) {
            $hcId = $payload['healthCenterId'] ?? null;
            
            // Legacy logic: if HC name provided but ID missing, find or create
            if (empty($hcId) && !empty($payload['healthCenterName'])) {
                $hc = HealthCenter::firstOrCreate(
                    ['Name' => $payload['healthCenterName']],
                    ['Address' => $payload['healthCenterAddress'] ?? '']
                );
                $hcId = $hc->HealthCenterID;
            }

            $requisition = Requisition::create([
                'RequisitionNumber' => 'TEMP-' . time(),
                'HealthCenterID' => $hcId,
                'UserID' => $userId,
                'RequestDate' => Carbon::now(),
                'StatusType' => 'Pending',
            ]);

            // Correct Requisition Number
            $reqNum = 'REQ-' . date('Y') . '-' . str_pad($requisition->RequisitionID, 5, '0', STR_PAD_LEFT);
            $requisition->update(['RequisitionNumber' => $reqNum]);

            foreach ($payload['items'] as $item) {
                if (empty($item['itemId']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                RequisitionItem::create([
                    'RequisitionID' => $requisition->RequisitionID,
                    'ItemID' => $item['itemId'],
                    'QuantityRequested' => (int)$item['quantity'],
                ]);
            }

            // 通知 Trigger: Notify Admin and Warehouse of new Central Requisition
            NotificationController::create(
                "New Requisition",
                "New requisition {$requisition->RequisitionNumber} submitted for processing.",
                "/requisitions",
                "Warehouse Staff"
            );
            NotificationController::create(
                "New Requisition",
                "Central requisition {$requisition->RequisitionNumber} is pending review.",
                "/requisitions",
                "Administrator"
            );

            return $requisition;
        });
    }

    public function createLocalRequisition(array $payload, int $healthCenterId, int $userId)
    {
        return DB::transaction(function () use ($payload, $healthCenterId, $userId) {
            $requisition = Requisition::create([
                'RequisitionNumber' => 'TEMP-L-' . time(),
                'HealthCenterID' => $healthCenterId,
                'UserID' => $userId,
                'RequestDate' => Carbon::now(),
                'StatusType' => 'Pending',
            ]);

            $reqNum = 'LREQ-' . date('Y') . '-' . str_pad($requisition->RequisitionID, 5, '0', STR_PAD_LEFT);
            $requisition->update(['RequisitionNumber' => $reqNum]);

            foreach ($payload['items'] as $item) {
                if (empty($item['itemId']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                RequisitionItem::create([
                    'RequisitionID' => $requisition->RequisitionID,
                    'ItemID' => $item['itemId'],
                    'QuantityRequested' => (int)$item['quantity'],
                ]);
            }

            // 通知 Trigger: Notify Admin and Warehouse of new Local Requisition
            NotificationController::create(
                "New Local Requisition",
                "Staff submitted a local requisition ({$requisition->RequisitionNumber}).",
                "/requisitions",
                "Warehouse Staff"
            );

            return $requisition;
        });
    }

    public function updateStatus(int $id, string $status, int $userId, array $itemStatuses = [], ?string $remarks = null)
    {
        return DB::transaction(function () use ($id, $status, $userId, $itemStatuses, $remarks) {
            $requisition = Requisition::findOrFail($id);
            $requisition->update(['StatusType' => $status]);

            // Log the decision
            DB::table('RequisitionApprovalLog')->insert([
                'RequisitionID' => $id,
                'UserID' => $userId,
                'Decision' => $status,
                'DecisionDate' => Carbon::now(),
                'Remarks' => $remarks ?: "Status updated to {$status} via central dashboard."
            ]);

            // Apply per-item statuses if provided
            if (!empty($itemStatuses)) {
                foreach ($itemStatuses as $itemId => $itemStatus) {
                    DB::table('RequisitionItem')
                        ->where('RequisitionItemID', $itemId)
                        ->update([
                            'ItemStatus' => $itemStatus,
                        ]);
                }
            }

            // 通知 Trigger: Notify Requester of status update
            NotificationController::create(
                "Requisition Update",
                "Your requisition #{$requisition->RequisitionNumber} has been {$status}.",
                "/requisitions",
                null,
                $requisition->UserID,
                $status === 'Rejected' ? 'High' : 'Normal'
            );

            return $requisition;
        });
    }
}
