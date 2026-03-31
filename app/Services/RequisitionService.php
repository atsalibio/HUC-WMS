<?php

namespace App\Services;

use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\System\HealthCenter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            return $requisition;
        });
    }

    public function updateStatus(int $id, string $status, int $userId)
    {
        return DB::transaction(function () use ($id, $status, $userId) {
            $requisition = Requisition::findOrFail($id);
            $requisition->update(['StatusType' => $status]);

            // Log the decision
            DB::table('RequisitionApprovalLog')->insert([
                'RequisitionID' => $id,
                'UserID' => $userId,
                'Decision' => $status,
                'DecisionDate' => Carbon::now(),
                'Remarks' => "Status updated to {$status} via central dashboard."
            ]);

            return $requisition;
        });
    }
}
