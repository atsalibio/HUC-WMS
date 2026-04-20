<?php

namespace App\Services;

use App\Models\HealthCenter\HCPatient;
use App\Models\HealthCenter\HCPatientRequisition;
use App\Models\HealthCenter\HCPatientRequisitionItem;
use App\Models\System\TransactionLog;
use App\Models\System\HealthCenter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientRequisitionService
{
    public function createPatient(array $data, $userId)
    {
        $patient = HCPatient::create($data);

        TransactionLog::create([
            'UserID' => $userId,
            'ReferenceType' => "Patient account created",
            'ReferenceID' => $patient->PatientID,
            'ActionType' => 'Patient Requisition',
            'ActionDetails' => "Created new patient account.",
            'ActionDate' => Carbon::now()
        ]);

        return $patient;
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
                'IDProof' => $data['idProofPath'] ?? null,
            ]);

            $reqNum = 'PREQ-' . date('Y') . '-' . str_pad($requisition->PatientReqID, 5, '0', STR_PAD_LEFT);
            $requisition->update(['RequisitionNumber' => $reqNum]);
            $healthCenter = HealthCenter::find($data['healthCenterId']);

            TransactionLog::create([
                'UserID' => $userId,
                'ReferenceType' => "'{$healthCenter->Name}' PatientRequisition created",
                'ReferenceID' => $requisition->RequisitionNumber,
                'ActionType' => 'Patient Requisition',
                'ActionDetails' => "Created new patient requisition '{$requisition->RequisitionNumber}' for {$healthCenter->Name}.",
                'ActionDate' => Carbon::now()
            ]);

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
            $healthCenter = HealthCenter::find($requisition->HealthCenterID);

            $requisition->StatusType = $status;
            $requisition->save();

            TransactionLog::create([
                'UserID' => $userId,
                'ReferenceType' => "'{$healthCenter->Name}' Patient Requisition {$status}",
                'ReferenceID' => $requisition->RequisitionNumber,
                'ActionType' => 'Patient Requisition',
                'ActionDetails' => "Updated patient requisition '{$requisition->RequisitionNumber}' for {$healthCenter->Name} to status '{$status}'.",
                'ActionDate' => Carbon::now()
            ]);

            return $requisition;
        });
    }

    public function dispense($reqId, $userId)
    {
        return DB::transaction(function () use ($reqId, $userId) {
            $requisition = HCPatientRequisition::findOrFail($reqId);
            $healthCenter = HealthCenter::find($requisition->HealthCenterID);

            $items = DB::table('hcpatientrequisitionitem as pri')
                ->join('hcinventorybatch as i', 'pri.ItemID', '=', 'i.ItemID')
                ->where('pri.PatientReqID', $reqId)
                ->select('pri.ItemID', 'pri.QuantityRequested', 'i.QuantityOnHand as InventoryOnHand')
                ->get();

            foreach ($items as $item) {
                $hcbatch = DB::table('hcinventorybatch as i')
                    ->join('centralinventorybatch as cib', 'i.BatchID', '=', 'cib.BatchID')
                    ->where('i.HealthCenterID', $requisition->HealthCenterID)
                    ->where('i.ItemID', $item->ItemID)
                    ->where('i.QuantityOnHand', '>', 0)
                    ->orderBy('cib.ExpiryDate', 'ASC')
                    ->get();

                $QuantityRemaining = $item->QuantityRequested;

                foreach ($hcbatch as $batch) {
                    if ($QuantityRemaining <= 0) break;

                    $dispenseQty = min($QuantityRemaining, $batch->QuantityOnHand);
                    DB::table('hcinventorybatch')
                        ->where('BatchID', $batch->BatchID)
                        ->decrement('QuantityOnHand', $dispenseQty);

                    $QuantityRemaining -= $dispenseQty;
                }
            }

            $requisition->StatusType = 'Completed';
            $requisition->save();

            return $requisition;
        });
    }
}
