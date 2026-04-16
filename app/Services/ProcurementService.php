<?php

namespace App\Services;

use App\Models\Procurement\ProcurementOrder;
use App\Models\Procurement\ProcurementOrderItem;
use App\Models\Procurement\Contract;
use App\Models\System\SecurityLog;
use App\Models\System\TransactionLog;
use App\Models\Procurement\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as LaravelRequest;
use App\Http\Controllers\NotificationController;

class ProcurementService
{
    public function createProcurementOrder(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // First check if a contract needs to be created
            $contractId = $data['contract_id'] ?? null;
            if (!empty($data['contract_number'])) {
                $contract = Contract::create([
                    'SupplierID' => $data['supplier_id'],
                    'ContractNumber' => $data['contract_number'],
                    'StartDate' => $data['contract_start_date'] ?? null,
                    'EndDate' => $data['contract_end_date'] ?? null,
                    'ContractAmount' => $data['contract_amount'] ?? 0,
                    'StatusType' => 'Active',
                ]);
                $contractId = $contract->ContractID;
            }
            $supplierData = Supplier::find($data['supplier_id']);

            $po = ProcurementOrder::create([
                'UserID' => $userId,
                'SupplierID' => $data['supplier_id'],
                'HealthCenterID' => $data['health_center_id'] ?? null,
                'ContractID' => $contractId,
                'PONumber' => 'PO-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'PODate' => Carbon::now(),
                'StatusType' => 'Pending',
                'DocumentType' => $data['document_type'] ?? 'PO',
                'PhotoPath' => $data['photo_path'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                ProcurementOrderItem::create([
                    'POID' => $po->POID,
                    'ItemID' => $item['itemId'],
                    'QuantityOrdered' => $item['quantity'],
                    'UnitCost' => $item['unitCost'] ?? 0,
                    'ExpiryDate' => $item['expiryDate'] ?? null,
                ]);
            }

            TransactionLog::create([
                'UserID' => $userId,
                'ReferenceType' => 'Procurement Order Created',
                'ReferenceID' => $po->PONumber,
                'ActionType' => 'Procurement Order',
                'ActionDetails' => "Created new procurement order PO '{$po->PONumber}'.",
                'ActionDate' => Carbon::now()
            ]);

            NotificationController::create(
                "New Procurement Order",
                "Order {$po->PONumber} was created for " . ($supplierData -> Name ?? 'new supplier') . ".",
                "/procurement",
                "Warehouse Staff"
            );

            NotificationController::create(
                "New Procurement Order",
                "Order {$po->PONumber} was created for " . ($supplierData -> Name ?? 'new supplier') . ".",
                "/procurement",
                "Administrator"
            );

            return $po;
        });
    }

    public function updatePOStatus($poId, $status, $userId)
    {
        $po = ProcurementOrder::findOrFail($poId);
        $po->StatusType = $status;
        $po->save();

        SecurityLog::create([
            'UserID' => $userId,
            'ActionType' => 'Procurement Order',
            'ActionDescription' => "Updated PO $poId status to $status",
            'IPAddress' => LaravelRequest::ip(),
            'ModuleAffected' => 'Procurement',
            'ActionDate' => Carbon::now(),
        ]);

        TransactionLog::create([
            'UserID' => $userId,
            'ReferenceType' => "Procurement Order {$status}",
            'ReferenceID' => $po->PONumber,
            'ActionType' => 'Procurement Order',
            'ActionDetails' => "Updated procurement order PO '{$po->PONumber}' status to {$status}.",
            'ActionDate' => Carbon::now()
        ]);

        // 通知 Trigger: Notify Requester of status update
        NotificationController::create(
            "Procurement Update",
            "Your order #{$po->PONumber} has been {$status}.",
            "/procurement",
            null,
            $po->UserID,
            $status === 'Rejected' ? 'High' : 'Normal'
        );

        return $po;
    }
}
