<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    protected $allowedPages = [
        'dashboard', 'requisitions', 'hc_patient_requisitions', 'hc_requisitions', 'patient_requisitions',
        'patient_requisitions_hp', 'procurement_orders', 'procurement-orders', 'receiving', 'inventory', 'hc_inventory', 'warehouse',
        'issuance', 'adjustments', 'reports', 'history', 'settings', 'dpri_import', 'suppliers', 'profile'
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
        if (!$user) return redirect()->route('login.show');

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
            $data['securityLogs'] = \App\Models\System\SecurityLog::where('UserID', $user->UserID)->with(['user'])->latest('ActionDate')->limit(10)->get();
        }

        if ($page === 'receiving') {
            $data['pendingOrders'] = \App\Models\Procurement\ProcurementOrder::where('StatusType', 'Approved')->with(['supplier', 'items.item'])->get();
            $data['warehouses'] = \App\Models\Procurement\Warehouse::all();
        }

        if ($page === 'requisitions') {
            $query = \App\Models\Requisition\Requisition::query();
            if ($isHCStaff && $hcId) {
                $query->where('HealthCenterID', $hcId);
            }
            $data['requisitions'] = $query->with(['healthCenter', 'items.item', 'user'])->latest('RequestDate')->get();
            $data['healthCenters'] = \App\Models\System\HealthCenter::all();
            $data['items'] = \App\Models\Inventory\Item::all();
        }

        if ($page === 'issuance') {
            $data['approvedRequisitions'] = \App\Models\Requisition\Requisition::whereIn('StatusType', ['Approved', 'Partial'])
                ->with(['healthCenter', 'items.item'])
                ->get();
            $data['warehouses'] = \App\Models\Procurement\Warehouse::all();
        }

        if ($page === 'hc_patient_requisitions') {
            $query = \App\Models\HealthCenter\HCPatientRequisition::query();
            $patientQuery = \App\Models\HealthCenter\HCPatient::query();

            if ($isHCStaff && $hcId) {
                $query->where('HealthCenterID', $hcId);
                $patientQuery->where('HealthCenterID', $hcId);
            }

            $data['patients'] = $patientQuery->get();
            $data['items'] = \App\Models\Inventory\Item::all();
            $data['requisitions'] = $query->with(['patient', 'items.item'])->latest('RequestDate')->get();
        }

        if ($page === 'patient_requisitions_hp') {
            $data['requisitions'] = \App\Models\HealthCenter\HCPatientRequisition::where('StatusType', 'Pending')
                ->with(['patient', 'healthCenter', 'items.item'])
                ->get();
        }

        if ($page === 'inventory') {
            $data['batches'] = \App\Models\Inventory\Batch::with(['item', 'warehouse'])->latest('DateReceived')->get();
        }

        if ($page === 'hc_inventory') {
            $data['hc_inventory'] = \App\Models\HealthCenter\HCInventoryBatch::with(['item', 'healthCenter'])->latest('DateReceivedAtHC')->get();
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
