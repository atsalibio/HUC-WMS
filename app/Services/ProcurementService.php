<?php

namespace App\Services;

use App\Models\Procurement\ProcurementOrder;
use App\Models\Procurement\ProcurementOrderItem;
use App\Models\Procurement\Contract;
use App\Models\System\SecurityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as LaravelRequest;

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

            $po = ProcurementOrder::create([
                'UserID' => $userId,
                'SupplierID' => $data['supplier_id'],
                'SupplierName' => $data['supplier_name'] ?? null,
                'SupplierAddress' => $data['supplier_address'] ?? null,
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

            SecurityLog::create([
                'UserID' => $userId,
                'ActionType' => 'Procurement Order',
                'ActionDescription' => 'Created New PO ' . $po->PONumber,
                'IPAddress' => LaravelRequest::ip(),
                'ModuleAffected' => 'Procurement',
                'ActionDate' => Carbon::now(),
            ]);

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

        return $po;
    }
}
