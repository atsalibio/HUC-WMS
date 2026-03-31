<?php

namespace App\Services;

use App\Models\HealthCenter\HCPatient;
use App\Models\HealthCenter\HCPatientRequisition;
use App\Models\HealthCenter\HCPatientRequisitionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientRequisitionService
{
    public function createPatient(array $data)
    {
        return HCPatient::create($data);
    }

    public function searchPatients(string $query, $healthCenterId, $role)
    {
        $q = HCPatient::query();

        if ($role === 'Health Center Staff') {
            $q->where('HealthCenterID', $healthCenterId);
        }

        if ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('FName', 'LIKE', "%{$query}%")
                    ->orWhere('LName', 'LIKE', "%{$query}%")
                    ->orWhere('PatientID', 'LIKE', "%{$query}%");
            });
        }

        return $q->orderBy('LName')->limit(10)->get();
    }

    public function createRequisition(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            $requisition = HCPatientRequisition::create([
                'PatientID' => $data['patientId'],
                'UserID' => $userId,
                'HealthCenterID' => $data['healthCenterId'],
                'RequisitionNumber' => 'PREQ-' . time(),
                'RequestDate' => Carbon::now(),
                'StatusType' => 'Pending',
                'Diagnosis' => $data['diagnosis'] ?? null,
                'Notes' => $data['notes'] ?? null,
                'ContactInfo' => $data['contactInfo'] ?? null,
                'IDProof' => $data['idProof'] ?? null,
            ]);

            $reqNum = 'PREQ-' . date('Y') . '-' . str_pad($requisition->PatientReqID, 5, '0', STR_PAD_LEFT);
            $requisition->update(['RequisitionNumber' => $reqNum]);

            foreach ($data['items'] as $item) {
                HCPatientRequisitionItem::create([
                    'PatientReqID' => $requisition->PatientReqID,
                    'ItemID' => $item['itemId'],
                    'QuantityRequested' => $item['quantity'],
                ]);
            }

            return $requisition;
        });
    }

    public function updateStatus($reqId, $status, $userId)
    {
        return DB::transaction(function () use ($reqId, $status, $userId) {
            $requisition = HCPatientRequisition::findOrFail($reqId);
            
            if ($status === 'Approved' && $requisition->StatusType !== 'Approved') {
                // Deduct from HC inventory
                $items = $requisition->items;
                foreach ($items as $item) {
                    $remaining = $item->QuantityRequested;
                    
                    $inventory = DB::table('HCInventoryBatch')
                        ->where('HealthCenterID', $requisition->HealthCenterID)
                        ->where('ItemID', $item->ItemID)
                        ->where('QuantityOnHand', '>', 0)
                        ->orderBy('ExpiryDate', 'ASC')
                        ->get();

                    foreach ($inventory as $inv) {
                        if ($remaining <= 0) break;
                        
                        $qtyToDeduct = min($remaining, $inv->QuantityOnHand);
                        DB::table('HCInventoryBatch')
                            ->where('HCBatchID', $inv->HCBatchID)
                            ->decrement('QuantityOnHand', $qtyToDeduct);
                        
                        $remaining -= $qtyToDeduct;
                    }

                    if ($remaining > 0) {
                        // Optional: Handle insufficient stock case
                        // throw new \Exception("Insufficient local stock for item ID " . $item->ItemID);
                    }
                }
            }

            $requisition->StatusType = $status;
            $requisition->save();

            return $requisition;
        });
    }
}
