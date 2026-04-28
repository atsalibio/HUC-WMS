<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PageController extends Controller
{
    protected $allowedPages = [
        'dashboard',
        'requisitions',
        'hc_patient_requisitions',
        'hc_requisitions',
        'patient_requisitions',
        'patient_requisitions_hp',
        'procurement_orders',
        'procurement-orders',
        'receiving',
        'inventory',
        'hc_inventory',
        'warehouse',
        'issuance',
        'adjustments',
        'reports',
        'history',
        'settings',
        'dpri_import',
        'suppliers',
        'profile'
    ];

    protected $pagePermissions = [
        'dashboard' => ['Administrator', 'Head Pharmacist', 'Health Center Staff', 'Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User'],
        'requisitions' => ['Administrator', 'Head Pharmacist', 'Health Center Staff'],
        'hc_patient_requisitions' => ['Administrator', 'Health Center Staff', 'Head Pharmacist'],
        'patient_requisitions_hp' => ['Administrator', 'Head Pharmacist'],
        'procurement_orders' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff'],
        'procurement-orders' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff'],
        'receiving' => ['Administrator', 'Warehouse Staff'],
        'inventory' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User'],
        'hc_inventory' => ['Administrator', 'Head Pharmacist', 'Health Center Staff'],
        'warehouse' => ['Administrator', 'Warehouse Staff'],
        'issuance' => ['Administrator', 'Warehouse Staff'],
        'adjustments' => ['Administrator', 'Head Pharmacist'],
        'reports' => ['Administrator', 'Accounting Office User', 'CMO/GSO/COA User', 'Head Pharmacist'],
        'history' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff'],
        'settings' => ['Administrator', 'Head Pharmacist', 'Health Center Staff', 'Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User'],
        'dpri_import' => ['Administrator', 'Head Pharmacist'],
        'suppliers' => ['Administrator', 'Head Pharmacist'],
        'profile' => ['Administrator', 'Head Pharmacist', 'Health Center Staff', 'Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User'],
    ];

    public function show($page)
    {
        if (!in_array($page, $this->allowedPages)) {
            abort(404);
        }

        $user = Auth::user();
        if (!$user)
            return redirect()->route('login.show');

        // Security: Role-based Gating
        $allowedRoles = $this->pagePermissions[$page] ?? [];
        if (!empty($allowedRoles) && !in_array($user->Role, $allowedRoles)) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to access this page.');
        }

        $hcId = $user->HealthCenterID;
        $isHCStaff = $user->Role === 'Health Center Staff';
        $isAdminOrHP = in_array($user->Role, ['Administrator', 'Head Pharmacist']);

        $currentPage = $page;
        $viewName = 'pages.' . str_replace('-', '_', $page);
        $data = ['currentPage' => $currentPage, 'user' => $user];

        if ($page === 'procurement-orders' || $page === 'procurement_orders') {
            $data['suppliers'] = \App\Models\Procurement\Supplier::all();
            $data['healthCenters'] = \App\Models\System\HealthCenter::all();
            $data['items'] = \App\Models\Inventory\Item::all();
            $data['procurementOrders'] = \App\Models\Procurement\ProcurementOrder::with(['supplier', 'healthCenter', 'items.item'])->latest('PODate')->get();
        }

        if ($page === 'settings') {
            $user = Auth::user();
            $data['logs'] = \App\Models\System\TransactionLog::where('UserID', $user->UserID)->with(['user'])->latest('ActionDate')->limit(10)->get();
            $data['securityLogs'] = \App\Models\System\SecurityLog::where('UserID', $user->UserID)
                ->whereIn('ActionType', ['Login', 'Logout', 'Profile Updated', 'Password Changed'])
                ->with(['user'])
                ->latest('ActionDate')
                ->limit(10)
                ->get();
        }

        if ($page === 'receiving') {
            $data['pendingOrders'] = \App\Models\Procurement\ProcurementOrder::where('StatusType', 'Approved')->with(['supplier', 'items.item'])->get();
            $data['receivingHistory'] = \App\Models\Procurement\Receiving::with(['procurementOrder.supplier', 'user', 'items.batch.item'])->latest('ReceivedDate')->limit(50)->get();
            $data['warehouses'] = \App\Models\Procurement\Warehouse::all();
        }

        if ($page === 'requisitions') {
            $query = \App\Models\Requisition\Requisition::query();
            if ($isHCStaff && $hcId) {
                $query->where('HealthCenterID', $hcId);
            }
            $data['requisitions'] = $query->with(['healthCenter', 'items.item', 'user', 'items.item'])->latest('RequestDate')->get();
            $data['healthCenters'] = \App\Models\System\HealthCenter::all();
            // Exclude Utility items from requisition form
            $data['items'] = \App\Models\Inventory\Item::whereNotIn('ItemType', ['Utility', 'Equipment'])->get();
            // Aggregate stock levels (non-expired batches only) per item
            $data['stockByItem'] = \App\Models\Inventory\Batch::where('QuantityOnHand', '>', 0)
                ->where('IsLocked', false)
                ->where(function ($q) {
                    $q->whereNull('ExpiryDate')->orWhere('ExpiryDate', '>=', \Carbon\Carbon::today());
                })
                ->selectRaw('ItemID, SUM(QuantityOnHand) as total')
                ->groupBy('ItemID')
                ->pluck('total', 'ItemID');
        }

        if ($page === 'issuance') {
            $data['approvedRequisitions'] = \App\Models\Requisition\Requisition::whereIn('StatusType', ['Approved', 'Partial'])
                ->with(['healthCenter', 'items.item'])
                ->get();
            $data['warehouses'] = \App\Models\Procurement\Warehouse::all();
            // Prepare available non-expired batches for each item, sorted FEFO
            $batchRows = \App\Models\Inventory\Batch::where('QuantityOnHand', '>', 0)
                ->where('IsLocked', false)
                ->where(function ($q) {
                    $q->whereNull('ExpiryDate')->orWhere('ExpiryDate', '>=', \Carbon\Carbon::today());
                })
                ->orderBy('ExpiryDate', 'asc')
                ->get()
                ->groupBy('ItemID');
            $data['batchesByItem'] = $batchRows;
        }

        if ($page === 'hc_patient_requisitions') {
            $query = \App\Models\HealthCenter\HCPatientRequisition::query();
            $patientQuery = \App\Models\HealthCenter\HCPatient::query()->with('healthCenter');
            $selectedPatientId = request()->query('patientId');
            $selectedPatient = null;

            if ($isHCStaff && $hcId) {
                $query->where('HealthCenterID', $hcId);
                $patientQuery->where('HealthCenterID', $hcId);
            }

            if ($selectedPatientId) {
                $selectedPatient = \App\Models\HealthCenter\HCPatient::with('healthCenter')->find($selectedPatientId);
                if ($selectedPatient && (!$isHCStaff || $selectedPatient->HealthCenterID === $hcId)) {
                    $query->where('PatientID', $selectedPatient->PatientID);
                } else {
                    $selectedPatient = null;
                }
            }

            $data['patients'] = $patientQuery->get();
            $data['selectedPatient'] = $selectedPatient;
            $data['items'] = \App\Models\Inventory\Item::whereNotIn('ItemType', ['Utility', 'Equipment'])->get();
            $data['requisitions'] = $query->with(['patient', 'healthCenter', 'items.item'])->latest('RequestDate')->get();

            // Fetch HC stock levels
            if ($hcId) {
                $hcStock = \App\Models\HealthCenter\HCInventoryBatch::where('HealthCenterID', $hcId)
                    ->where('QuantityOnHand', '>', 0)
                    ->select('ItemID', \DB::raw('SUM(QuantityOnHand) as total'))
                    ->groupBy('ItemID')
                    ->pluck('total', 'ItemID');
                $data['hcStock'] = $hcStock;
            } else {
                $data['hcStock'] = [];
            }
        }

        if ($page === 'patient_requisitions_hp') {
            $data['requisitions'] = \App\Models\HealthCenter\HCPatientRequisition::where('StatusType', 'Pending')
                ->with(['patient', 'healthCenter', 'items.item'])
                ->get();
        }

        if ($page === 'inventory') {
            // Sort by FEFO (earliest expiry first), also exclude expired & out-of-stock items
            $allItems = \App\Models\Inventory\Item::all();
            $allBatches = \App\Models\Inventory\Batch::with(['item', 'warehouse'])
                ->where('QuantityOnHand', '>', 0)
                ->where('IsLocked', false)
                ->where(function ($q) {
                    $q->whereNull('ExpiryDate')->orWhere('ExpiryDate', '>=', \Carbon\Carbon::today());
                })
                ->orderBy('ExpiryDate', 'asc')  // FEFO
                ->get();

            // Compute last issuance date per item via IssuanceItem → Issuance → Batch
            $lastIssuanceByItem = \Illuminate\Support\Facades\DB::table('IssuanceItem as ii')
                ->join('Issuance as i', 'ii.IssuanceID', '=', 'i.IssuanceID')
                ->join('CentralInventoryBatch as b', 'ii.BatchID', '=', 'b.BatchID')
                ->selectRaw('b.ItemID, MAX(i.IssueDate) as LastIssuance')
                ->groupBy('b.ItemID')
                ->pluck('LastIssuance', 'ItemID');

            $batchesByItem = [];
            foreach ($allBatches as $batch) {
                $batchesByItem[$batch->ItemID][] = [
                    'BatchID'       => $batch->BatchID,
                    'LotNumber'     => $batch->LotNumber,
                    'BatchNumber'   => $batch->BatchNumber,
                    'ExpiryDate'    => $batch->ExpiryDate,
                    'DateReceived'  => $batch->DateReceived,
                    'QuantityOnHand' => $batch->QuantityOnHand,
                    'UnitCost'      => $batch->UnitCost ?? 0,
                    'PONumber'      => $batch->PONumber ?? null,
                ];
            }

            $aggregatedInventory = [];
            foreach ($allItems as $item) {
                $itemId = $item->ItemID;
                $itemBatches = $batchesByItem[$itemId] ?? [];
                $totalQty = array_sum(array_column($itemBatches, 'QuantityOnHand'));
                if ($totalQty <= 0)
                    continue; // skip out-of-stock
                $nextExpiry = null;
                if (!empty($itemBatches)) {
                    // First element is earliest (FEFO already sorted)
                    $nextExpiry = $itemBatches[0]['ExpiryDate'];
                }
                $aggregatedInventory[] = [
                    'ItemID'       => $itemId,
                    'ItemName'     => $item->ItemName,
                    'Brand'        => $item->Brand ?? '',
                    'DosageUnit'   => $item->DosageUnit ?? '',
                    'Category'     => $item->ItemType ?? 'N/A',
                    'Unit'         => $item->UnitOfMeasure ?? 'N/A',
                    'TotalQuantity' => $totalQty,
                    'NextExpiry'   => $nextExpiry,
                    'LastIssuance' => $lastIssuanceByItem[$itemId] ?? null,
                ];
            }

            $data['aggregatedInventory'] = $aggregatedInventory;
            $data['batchesByItem'] = $batchesByItem;
            $data['batches'] = $allBatches;
        }

        if ($page === 'hc_inventory') {
            $raw = \App\Models\HealthCenter\HCInventoryBatch::with(['item', 'healthCenter'])->latest('DateReceivedAtHC')->get();
            $data['hc_inventory'] = $raw->map(function ($row) {
                return [
                    'HCBatchID'        => $row->HCBatchID,
                    'HealthCenterID'   => $row->HealthCenterID,
                    'HealthCenterName' => $row->healthCenter?->Name ?? '—',
                    'ItemID'           => $row->ItemID,
                    'BatchID'          => $row->BatchID,
                    'ExpiryDate'       => $row->ExpiryDate,
                    'QuantityOnHand'   => $row->QuantityOnHand,
                    'UnitCost'         => $row->UnitCost,
                    'DateReceivedAtHC' => $row->DateReceivedAtHC,
                    'LotNumber'        => $row->LotNumber ?? null,
                    'ItemName'         => $row->item?->ItemName ?? '—',
                    'ItemType'         => $row->item?->ItemType ?? '—',
                    'UnitOfMeasure'    => $row->item?->UnitOfMeasure ?? '—',
                ];
            })->values();
            $data['hcId']          = $hcId;
            $data['healthCenters'] = \App\Models\System\HealthCenter::all();
            $data['userRole']      = $user->Role;
        }

        if ($page === 'suppliers') {
            $data['suppliers'] = \App\Models\Procurement\Supplier::all();
        }

        if ($page === 'reports') {
            $data['recentLogs'] = \App\Models\System\SecurityLog::with('user')->latest('ActionDate')->limit(50)->get();
        }

        if ($page === 'history') {
            $data['transactions'] = \App\Models\System\TransactionLog::with(['user'])->latest('ActionDate')->get();
        }

        if ($page === 'adjustments') {
            $data['items'] = \App\Models\Inventory\Item::all();
            $data['warehouses'] = \App\Models\Procurement\Warehouse::all();
        }

        if (view()->exists($viewName)) {
            return view($viewName, $data);
        }

        return view('pages.placeholder', $data);
    }
}
