<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Inventory\Adjustment;
use App\Models\Issuance\Issuance;
use App\Models\Requisition\Requisition;
use App\Models\HealthCenter\HCPatientRequisition;
use App\Models\Procurement\ProcurementOrder;
use App\Models\System\SecurityLog;
use App\Models\System\TransactionLog;
use App\Models\HealthCenter\HCInventoryBatch;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('pages.history');
    }

    public function getHistory(Request $request)
    {
        $type = $request->type ?? 'summary';
        $data = [];

        switch ($type) {
            case 'summary':
                // Item additions (Central) aggregate manually since QuantityReceived is not a column in CentralInventoryBatch
                $data = Item::with('batches')
                    ->get()
                    ->transform(function($item) {
                        $totalAdded = $item->batches->sum(function ($b) {
                            return $b->QuantityOnHand + $b->QuantityReleased;
                        });
                        return [
                            'ItemID' => $item->ItemID,
                            'ItemName' => $item->ItemName,
                            'TotalAdded' => $totalAdded,
                            'TotalTransactions' => $item->batches->count(),
                            'LastReceived' => $item->batches->sortByDesc('DateReceived')->first()?->DateReceived
                        ];
                    });
                break;

            case 'item_additions':
                // Item additions (Central Arrivals) - Filter out records without receiving parent
                $data = \App\Models\Procurement\ReceivingItem::with(['receiving.user', 'batch.item'])
                    ->has('receiving')
                    ->orderBy('ReceivingItemID', 'desc')
                    ->get()
                    ->transform(function($ri) {
                        $u = $ri->receiving?->user;
                        return [
                            'Date' => $ri->receiving?->ReceivedDate ?? now(),
                            'ItemName' => $ri->batch?->item?->ItemName ?? 'Unknown Item',
                            'BatchID' => $ri->BatchID,
                            'Quantity' => $ri->QuantityReceived ?? 0,
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'warehouse_issuances':
                $data = Issuance::with(['requisition.healthCenter', 'user', 'items.batch.item'])
                    ->orderBy('IssueDate', 'desc')
                    ->get()
                    ->flatMap(function($iss) {
                        return $iss->items->map(function($item) use ($iss) {
                            $u = $iss->user;
                            return [
                                'Date' => $iss->IssueDate ?? now(),
                                'ItemName' => $item->batch?->item?->ItemName ?? 'Undefined Item',
                                'Quantity' => -1 * abs($item->QuantityIssued ?? 0),
                                'HealthCenter' => $iss->requisition?->healthCenter?->Name ?? 'N/A',
                                'Reference' => '#' . $iss->IssuanceID,
                                'BatchID' => $item->BatchID,
                                'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                            ];
                        });
                    });
                break;

            case 'patient_list':
                $data = HCPatientRequisition::with(['patient', 'user', 'items.item', 'healthCenter'])
                    ->orderBy('RequestDate', 'desc')
                    ->get()
                    ->transform(function($req) {
                        $u = $req->user;
                        return [
                            'Date' => $req->RequestDate ?? now(),
                            'ItemName' => $req->items->pluck('item.ItemName')->filter()->implode(', ') ?: 'General Distribution',
                            'Quantity' => -1 * abs($req->items->sum('QuantityRequested') ?? 0),
                            'Patient' => $req->patient ? ($req->patient->FName . ' ' . $req->patient->LName) : ($req->ManualName ?? 'Walk-in'),
                            'HealthCenter' => $req->healthCenter?->Name ?? 'HC Site',
                            'Reference' => $req->RequisitionNumber ?? 'DISPENSE',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'adjustments':
                $data = Adjustment::with(['batch.item', 'user'])
                    ->orderBy('AdjustmentDate', 'desc')
                    ->get()
                    ->transform(function($adj) {
                        $u = $adj->user;
                        return [
                            'Date' => $adj->AdjustmentDate ?? now(),
                            'ItemName' => $adj->batch?->item?->ItemName ?? 'Item Record',
                            'Quantity' => $adj->AdjustmentType === 'Return' ? abs($adj->AdjustmentQuantity ?? 0) : -1 * abs($adj->AdjustmentQuantity ?? 0),
                            'Reason' => $adj->Reason ?? 'Operational Correction',
                            'Reference' => $adj->AdjustmentType ?? 'Manual',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'procurement_orders':
                $data = ProcurementOrder::with(['user', 'supplier', 'items'])
                    ->orderBy('PODate', 'desc')
                    ->get()
                    ->transform(function($po) {
                        $u = $po->user;
                        return [
                            'Date' => $po->PODate ?? now(),
                            'ItemName' => $po->PONumber ?? 'PO Record',
                            'Quantity' => $po->items->sum('QuantityOrdered') ?? 0,
                            'HealthCenter' => $po->supplier?->Name ?? 'Contractor',
                            'Reference' => $po->PONumber,
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'security_log':
                $data = SecurityLog::with('user')
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        $u = $log->user;
                        return [
                            'Date' => $log->ActionDate ?? now(),
                            'ItemName' => $log->ActionType ?? 'System Event',
                            'Quantity' => 0,
                            'HealthCenter' => $log->ModuleAffected ?? 'Security',
                            'Reference' => $log->IPAddress ?? '127.0.0.1',
                            'Reason' => $log->ActionDescription ?? 'Audit Trail Log',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'patient_log':
                // Broaden search to include anything related to dispensing or patients in history
                $data = TransactionLog::where('ReferenceType', 'LIKE', '%Patient%')
                    ->orWhere('ActionType', 'LIKE', '%Dispense%')
                    ->with('user')
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        $u = $log->user;
                        return [
                            'Date' => $log->ActionDate ?? now(),
                            'ItemName' => $log->ActionType ?? 'Patient Activity',
                            'Quantity' => 0,
                            'HealthCenter' => $log->ReferenceID ?? 'HC',
                            'Reference' => $log->ReferenceType ?? 'Log',
                            'Reason' => $log->ActionDetails ?? 'Service rendered',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'inventory_count':
                $data = Batch::with('item')
                    ->select('ItemID', DB::raw('SUM(QuantityOnHand) as total_qty'), DB::raw('MAX(DateReceived) as last_arrival'))
                    ->groupBy('ItemID')
                    ->get()
                    ->transform(function($b) {
                        return [
                            'Date' => $b->last_arrival ?? now(),
                            'ItemName' => $b->item?->ItemName ?? 'Stock Entry',
                            'Quantity' => $b->total_qty ?? 0,
                            'HealthCenter' => 'Central Warehouse',
                            'Reference' => 'Audit Count',
                            'User' => 'System'
                        ];
                    });
                break;

            case 'hc_inventory_count':
                $data = HCInventoryBatch::with(['item', 'healthCenter'])
                    ->select('HealthCenterID', 'ItemID', DB::raw('SUM(QuantityOnHand) as total_qty'), DB::raw('MAX(DateReceivedAtHC) as last_arrival'))
                    ->groupBy('HealthCenterID', 'ItemID')
                    ->get()
                    ->transform(function($b) {
                        return [
                            'Date' => $b->last_arrival ?? now(),
                            'ItemName' => $b->item?->ItemName ?? 'HC Stock',
                            'Quantity' => $b->total_qty ?? 0,
                            'HealthCenter' => $b->healthCenter?->Name ?? 'HC Site',
                            'Reference' => 'HC Audit',
                            'User' => 'System'
                        ];
                    });
                break;

            case 'hc_arrivals':
                $data = HCInventoryBatch::with(['item', 'healthCenter'])
                    ->whereNotNull('DateReceivedAtHC')
                    ->orderBy('DateReceivedAtHC', 'desc')
                    ->get()
                    ->transform(function($b) {
                        return [
                            'Date' => $b->DateReceivedAtHC ?? now(),
                            'ItemName' => $b->item?->ItemName ?? 'Medical Stock',
                            'Quantity' => $b->QuantityReceived ?? 0,
                            'HealthCenter' => $b->healthCenter?->Name ?? 'HC Site',
                            'Reference' => 'Arrival',
                            'User' => 'System'
                        ];
                    });
                break;

            case 'requisitions':
                $data = Requisition::with(['healthCenter', 'user', 'items'])
                    ->orderBy('RequestDate', 'desc')
                    ->get()
                    ->transform(function($req) {
                        $u = $req->user;
                        return [
                            'Date' => $req->RequestDate ?? now(),
                            'ItemName' => $req->RequisitionNumber ?? 'REQ',
                            'Quantity' => $req->items->sum('QuantityRequested') ?? 0,
                            'HealthCenter' => $req->healthCenter?->Name ?? 'HC Site',
                            'Reference' => $req->StatusType ?? 'Pending',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;
            
            case 'login_log':
                $data = SecurityLog::with('user')
                    ->whereIn('ActionType', ['Login', 'Logout'])
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        $u = $log->user;
                        
                        $device = 'Desktop';
                        if (preg_match('/\((Desktop|Mobile)\)/i', $log->ActionDescription, $matches)) {
                            $device = $matches[1];
                        }
                        
                        return [
                            'Date' => $log->ActionDate ?? now(),
                            'ItemName' => $log->ActionType ?? 'Session',
                            'Quantity' => 0,
                            'HealthCenter' => $log->IPAddress ?? 'Local',
                            'Device' => $device,
                            'Reference' => $log->IPAddress ?? 'N/A',
                            'Reason' => $log->ActionDescription ?? 'User Session Event',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;

            case 'audit_log':
                $data = TransactionLog::with('user')
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        $u = $log->user;
                        return [
                            'Date' => $log->ActionDate ?? now(),
                            'ItemName' => $log->ActionType ?? 'System Event',
                            'Quantity' => 0,
                            'HealthCenter' => $log->ReferenceType ?? 'General',
                            'Reference' => $log->ReferenceID ?? 'N/A',
                            'Reason' => $log->ActionDetails ?? 'Audit trail entry',
                            'User' => $u ? ($u->FName . ' ' . $u->LName . ' (' . $u->Username . ')') : 'System'
                        ];
                    });
                break;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
