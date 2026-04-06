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
                // Item Additons (Central)
                $data = Item::withCount(['batches as TotalTransactions'])
                    ->withSum('batches as TotalAdded', 'QuantityReceived')
                    ->get()
                    ->transform(function($item) {
                        return [
                            'ItemID' => $item->ItemID,
                            'ItemName' => $item->ItemName,
                            'TotalAdded' => $item->TotalAdded ?? 0,
                            'TotalTransactions' => $item->TotalTransactions,
                            'LastReceived' => $item->batches()->orderBy('DateReceived', 'desc')->first()?->DateReceived
                        ];
                    });
                break;

            case 'item_additions':
                $data = Batch::with(['item', 'user'])
                    ->orderBy('DateReceived', 'desc')
                    ->get()
                    ->transform(function($b) {
                        return [
                            'Date' => $b->DateReceived,
                            'ItemName' => $b->item?->ItemName,
                            'BatchID' => $b->BatchID,
                            'Quantity' => $b->QuantityReceived,
                            'User' => $b->user?->FName . ' ' . $b->user?->LName
                        ];
                    });
                break;

            case 'warehouse_issuances':
                $data = Issuance::with(['requisition.healthCenter', 'user', 'items.item'])
                    ->orderBy('IssueDate', 'desc')
                    ->get()
                    ->flatMap(function($iss) {
                        return $iss->items->map(function($item) use ($iss) {
                            return [
                                'Date' => $iss->IssueDate,
                                'ItemName' => $item->item?->ItemName,
                                'Quantity' => $item->QuantityIssued * -1,
                                'HealthCenter' => $iss->requisition?->healthCenter?->Name,
                                'Reference' => '#' . $iss->IssuanceID,
                                'BatchID' => $item->BatchID,
                                'User' => $iss->user?->FName . ' ' . $iss->user?->LName
                            ];
                        });
                    });
                break;

            case 'patient_list':
                $data = HCPatientRequisition::with(['patient', 'user', 'items.item', 'healthCenter'])
                    ->orderBy('RequestDate', 'desc')
                    ->get()
                    ->transform(function($req) {
                        return [
                            'Date' => $req->RequestDate,
                            'ItemName' => $req->items->pluck('item.ItemName')->implode(', '),
                            'Quantity' => $req->items->sum('QuantityRequested'),
                            'Patient' => $req->patient ? ($req->patient->FName . ' ' . $req->patient->LName) : $req->ManualName,
                            'HealthCenter' => $req->healthCenter?->Name,
                            'Reference' => $req->RequisitionNumber,
                            'User' => $req->user?->FName . ' ' . $req->user?->LName
                        ];
                    });
                break;

            case 'adjustments':
                $data = Adjustment::with(['batch.item', 'user'])
                    ->orderBy('AdjustmentDate', 'desc')
                    ->get()
                    ->transform(function($adj) {
                        return [
                            'Date' => $adj->AdjustmentDate,
                            'ItemName' => $adj->batch?->item?->ItemName,
                            'Quantity' => $adj->AdjustmentQuantity,
                            'Reason' => $adj->Reason,
                            'Reference' => $adj->AdjustmentType,
                            'User' => $adj->user?->FName . ' ' . $adj->user?->LName
                        ];
                    });
                break;

            case 'procurement_orders':
                $data = ProcurementOrder::with(['user', 'supplier'])
                    ->orderBy('PODate', 'desc')
                    ->get()
                    ->transform(function($po) {
                        return [
                            'Date' => $po->PODate,
                            'ItemName' => $po->PONumber,
                            'Quantity' => $po->items->sum('QuantityOrdered'),
                            'HealthCenter' => $po->supplier?->Name ?? 'Contractor',
                            'Reference' => $po->PONumber,
                            'User' => $po->user?->FName . ' ' . $po->user?->LName
                        ];
                    });
                break;

            case 'security_log':
                $data = SecurityLog::with('user')
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        return [
                            'Date' => $log->ActionDate,
                            'ItemName' => $log->ActionType,
                            'Quantity' => 0,
                            'HealthCenter' => $log->ModuleAffected,
                            'Reference' => $log->IPAddress,
                            'Reason' => $log->ActionDescription,
                            'User' => $log->user ? ($log->user->FName . ' ' . $log->user->LName) : 'System'
                        ];
                    });
                break;

            case 'patient_log':
                $data = TransactionLog::where('ReferenceType', 'LIKE', '%Patient%')
                    ->with('user')
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        return [
                            'Date' => $log->ActionDate,
                            'ItemName' => $log->ActionType,
                            'Quantity' => 0,
                            'HealthCenter' => $log->ReferenceID,
                            'Reference' => $log->ReferenceType,
                            'Reason' => $log->ActionDetails,
                            'User' => $log->user ? ($log->user->FName . ' ' . $log->user->LName) : 'System'
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
                            'Date' => $b->last_arrival,
                            'ItemName' => $b->item?->ItemName,
                            'Quantity' => $b->total_qty,
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
                            'Date' => $b->last_arrival,
                            'ItemName' => $b->item?->ItemName,
                            'Quantity' => $b->total_qty,
                            'HealthCenter' => $b->healthCenter?->Name,
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
                            'Date' => $b->DateReceivedAtHC,
                            'ItemName' => $b->item?->ItemName,
                            'Quantity' => $b->QuantityReceived,
                            'HealthCenter' => $b->healthCenter?->Name,
                            'Reference' => 'Arrival',
                            'User' => 'System'
                        ];
                    });
                break;

            case 'requisitions':
                $data = Requisition::with(['healthCenter', 'user'])
                    ->orderBy('RequestDate', 'desc')
                    ->get()
                    ->transform(function($req) {
                        return [
                            'Date' => $req->RequestDate,
                            'ItemName' => $req->RequisitionNumber,
                            'Quantity' => $req->items->sum('QuantityRequested'),
                            'HealthCenter' => $req->healthCenter?->Name,
                            'Reference' => $req->StatusType,
                            'User' => $req->user?->FName . ' ' . $req->user?->LName
                        ];
                    });
                break;
            
            case 'login_log':
                $data = SecurityLog::with('user')
                    ->whereIn('ActionType', ['Login', 'Logout'])
                    ->orderBy('ActionDate', 'desc')
                    ->get()
                    ->transform(function($log) {
                        return [
                            'Date' => $log->ActionDate,
                            'ItemName' => $log->ActionType,
                            'Quantity' => 0,
                            'HealthCenter' => $log->IPAddress,
                            'Reference' => $log->IPAddress,
                            'Reason' => 'Session Gateway',
                            'User' => $log->user ? ($log->user->FName . ' ' . $log->user->LName) : 'System'
                        ];
                    });
                break;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
