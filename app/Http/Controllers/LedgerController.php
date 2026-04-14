<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Return a paginated list of all ledger entries filtered by type.
     * Types: stock_in, stock_out, transfer, all
     */
    public function index(Request $request)
    {
        $type      = $request->input('type', 'all');
        $reference = $request->input('reference'); // Filter by PO or REQ number
        $perPage   = (int)$request->input('per_page', 20);

        $rows = collect();

        // Stock IN (Receivings)
        if (in_array($type, ['all', 'stock_in'])) {
            $query = DB::table('Receiving')
                ->join('ProcurementOrder', 'Receiving.POID', '=', 'ProcurementOrder.POID')
                ->join('ReceivingItem', 'ReceivingItem.ReceivingID', '=', 'Receiving.ReceivingID')
                ->join('CentralInventoryBatch', 'CentralInventoryBatch.BatchID', '=', 'ReceivingItem.BatchID')
                ->join('Item', 'Item.ItemID', '=', 'CentralInventoryBatch.ItemID')
                ->leftJoin('User', 'User.UserID', '=', 'Receiving.UserID');

            if ($reference) {
                $query->where('ProcurementOrder.PONumber', 'LIKE', "%{$reference}%");
            }

            $stockIn = $query->select([
                    'Receiving.ReceivingID as id',
                    DB::raw("'stock_in' as transaction_type"),
                    'Item.ItemName as item_name',
                    'CentralInventoryBatch.LotNumber as lot_number',
                    'CentralInventoryBatch.ExpiryDate as expiry_date',
                    'ReceivingItem.QuantityReceived as quantity',
                    'Receiving.ReceivedDate as transaction_date',
                    DB::raw("CONCAT(User.FName, ' ', User.LName) as performed_by"),
                    DB::raw("'Warehouse' as source"),
                    'ProcurementOrder.PONumber as reference',
                ])
                ->get();
            $rows = $rows->merge($stockIn);
        }

        // Stock OUT (Issuances)
        if (in_array($type, ['all', 'stock_out'])) {
            $query = DB::table('Issuance')
                ->join('RequisitionIssuanceItem', 'RequisitionIssuanceItem.IssuanceID', '=', 'Issuance.IssuanceID')
                ->join('CentralInventoryBatch', 'CentralInventoryBatch.BatchID', '=', 'RequisitionIssuanceItem.BatchID')
                ->join('Item', 'Item.ItemID', '=', 'CentralInventoryBatch.ItemID')
                ->join('Requisition', 'Requisition.RequisitionID', '=', 'Issuance.RequisitionID')
                ->leftJoin('HealthCenter', 'HealthCenter.HealthCenterID', '=', 'Requisition.HealthCenterID')
                ->leftJoin('User', 'User.UserID', '=', 'Issuance.UserID');

            if ($reference) {
                $query->where('Requisition.RequisitionNumber', 'LIKE', "%{$reference}%");
            }

            $stockOut = $query->select([
                    'Issuance.IssuanceID as id',
                    DB::raw("'stock_out' as transaction_type"),
                    'Item.ItemName as item_name',
                    'CentralInventoryBatch.LotNumber as lot_number',
                    'CentralInventoryBatch.ExpiryDate as expiry_date',
                    'RequisitionIssuanceItem.QuantityIssued as quantity',
                    'Issuance.IssueDate as transaction_date',
                    DB::raw("CONCAT(User.FName, ' ', User.LName) as performed_by"),
                    'HealthCenter.Name as source',
                    'Requisition.RequisitionNumber as reference',
                ])
                ->get();
            $rows = $rows->merge($stockOut);
        }

        // Inventory Adjustments
        if (in_array($type, ['all', 'adjustment'])) {
            $query = DB::table('InventoryAdjustment')
                ->join('CentralInventoryBatch', 'CentralInventoryBatch.BatchID', '=', 'InventoryAdjustment.BatchID')
                ->join('Item', 'Item.ItemID', '=', 'CentralInventoryBatch.ItemID')
                ->leftJoin('User', 'User.UserID', '=', 'InventoryAdjustment.AdjustedBy');

            $adjustments = $query->select([
                    'InventoryAdjustment.AdjustmentID as id',
                    DB::raw("'adjustment' as transaction_type"),
                    'Item.ItemName as item_name',
                    'CentralInventoryBatch.LotNumber as lot_number',
                    'CentralInventoryBatch.ExpiryDate as expiry_date',
                    'InventoryAdjustment.AdjustmentQuantity as quantity',
                    'InventoryAdjustment.AdjustmentDate as transaction_date',
                    DB::raw("CONCAT(User.FName, ' ', User.LName) as performed_by"),
                    'InventoryAdjustment.AdjustmentType as source',
                    'InventoryAdjustment.Reason as reference',
                ])
                ->get();
            $rows = $rows->merge($adjustments);
        }

        // Patient Dispensing (Health Center)
        if (in_array($type, ['all', 'stock_out'])) {
            $query = DB::table('hc_patient_requisitions')
                ->join('hc_patient_requisition_items', 'hc_patient_requisitions.PatientReqID', '=', 'hc_patient_requisition_items.PatientReqID')
                ->join('hc_patients', 'hc_patients.PatientID', '=', 'hc_patient_requisitions.PatientID')
                ->join('Item', 'Item.ItemID', '=', 'hc_patient_requisition_items.ItemID')
                ->leftJoin('HealthCenter', 'HealthCenter.HealthCenterID', '=', 'hc_patient_requisitions.HealthCenterID')
                ->leftJoin('User', 'User.UserID', '=', 'hc_patient_requisitions.UserID')
                ->where('hc_patient_requisitions.StatusType', 'Completed');

            $dispensing = $query->select([
                    'hc_patient_requisitions.PatientReqID as id',
                    DB::raw("'dispensing' as transaction_type"),
                    'Item.ItemName as item_name',
                    DB::raw("'Local Stock' as lot_number"),
                    DB::raw("NULL as expiry_date"),
                    'hc_patient_requisition_items.QuantityRequested as quantity',
                    'hc_patient_requisitions.RequestDate as transaction_date',
                    DB::raw("CONCAT(User.FName, ' ', User.LName) as performed_by"),
                    'HealthCenter.Name as source',
                    'hc_patient_requisitions.RequisitionNumber as reference',
                ])
                ->get();
            $rows = $rows->merge($dispensing);
        }

        // Sort by date descending
        $sorted = $rows->sortByDesc('transaction_date')->values();

        // Manual paginate
        $page = (int)$request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginated = $sorted->slice($offset, $perPage)->values();

        return response()->json([
            'data' => $paginated,
            'total' => $sorted->count(),
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($sorted->count() / $perPage),
        ]);
    }

    /**
     * Summary stats for the ledger dashboard.
     */
    public function summary()
    {
        $totalIn  = DB::table('ReceivingItem')->sum('QuantityReceived');
        $totalOut = DB::table('RequisitionIssuanceItem')->sum('QuantityIssued');

        // Add Adjustments to summary
        $adjustments = DB::table('InventoryAdjustment')->get();
        foreach ($adjustments as $adj) {
            if ($adj->AdjustmentType === 'Return' || $adj->AdjustmentQuantity > 0) {
                $totalIn += abs($adj->AdjustmentQuantity);
            } else {
                $totalOut += abs($adj->AdjustmentQuantity);
            }
        }

        // Add Patient Dispensing to summary
        $totalDispensed = DB::table('hc_patient_requisitions')
            ->join('hc_patient_requisition_items', 'hc_patient_requisitions.PatientReqID', '=', 'hc_patient_requisition_items.PatientReqID')
            ->where('hc_patient_requisitions.StatusType', 'Completed')
            ->sum('hc_patient_requisition_items.QuantityRequested');
        
        $totalOut += $totalDispensed;

        $expiringSoon = DB::table('CentralInventoryBatch')
            ->where('QuantityOnHand', '>', 0)
            ->whereBetween('ExpiryDate', [Carbon::today(), Carbon::today()->addDays(90)])
            ->count();

        $expired = DB::table('CentralInventoryBatch')
            ->where('QuantityOnHand', '>', 0)
            ->where('ExpiryDate', '<', Carbon::today())
            ->count();

        return response()->json([
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'expiring_soon' => $expiringSoon,
            'expired_batches' => $expired,
        ]);
    }
}
