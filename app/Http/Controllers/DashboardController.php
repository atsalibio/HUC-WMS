<?php

namespace App\Http\Controllers;

use App\Models\Inventory\Batch;
use App\Models\Procurement\ProcurementOrder;
use App\Models\Requisition\Requisition;
use App\Models\System\Notification;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Sample: Logic to auto-generate a notification for low stock
        $lowStockCount = Batch::where('QuantityOnHand', '<', 500)->where('IsLocked', false)->count();
        if ($lowStockCount > 0 && optional($user)->Role !== 'Health Center Staff') {
            Notification::firstOrCreate(
                ['Title' => 'Low Stock Warning', 'IsRead' => false, 'TargetRole' => 'Head Pharmacist'],
                [
                    'Message' => "There are {$lowStockCount} batches below the safety threshold.",
                    'Link' => route('page.show', ['page' => 'inventory']),
                    'Priority' => 'High'
                ]
            );
        }

        $requisitionsQuery = Requisition::query();
        $procurementQuery = ProcurementOrder::query();
        $patientReqQuery = \App\Models\HealthCenter\HCPatientRequisition::query();

        if ($user && $user->Role === 'Health Center Staff' && $user->HealthCenterID) {
            $requisitionsQuery->where('HealthCenterID', $user->HealthCenterID);
            $procurementQuery->where('HealthCenterID', $user->HealthCenterID);
            $patientReqQuery->where('HealthCenterID', $user->HealthCenterID);
        }

        $isHC = $user && $user->Role === 'Health Center Staff';

        $stats = [
            'pending_reqs' => (clone $requisitionsQuery)->where('StatusType', 'Pending')->count(),
            'completed_reqs' => (clone $requisitionsQuery)->where('StatusType', 'Completed')->count(),
            'pending_pos' => $isHC ? 0 : (clone $procurementQuery)->where('StatusType', 'Pending')->count(),
            'completed_pos' => $isHC ? 0 : (clone $procurementQuery)->where('StatusType', 'Completed')->count(),
            'low_stock' => $isHC ? 0 : Batch::where('QuantityOnHand', '<', 500)->where('IsLocked', false)->count(),
            'pending_patient_reqs' => (clone $patientReqQuery)->where('StatusType', 'Pending')->count(),
        ];

        $recentRequisitions = $requisitionsQuery->with('healthCenter')->orderBy('RequestDate', 'desc')->limit(5)->get();

        $pendingPOs = collect();
        if (in_array(optional($user)->Role, ['Administrator', 'Head Pharmacist', 'Warehouse Staff', 'Accounting Office User'])) {
            $pendingPOs = (clone $procurementQuery)->with(['supplier', 'healthCenter'])->where('StatusType', 'Pending')->orderBy('PODate', 'desc')->limit(5)->get();
        }

        $pendingPatientReqs = collect();
        if (in_array(optional($user)->Role, ['Administrator', 'Head Pharmacist'])) {
            $pendingPatientReqs = \App\Models\HealthCenter\HCPatientRequisition::with(['patient', 'healthCenter'])->where('StatusType', 'Pending')->latest('RequestDate')->limit(5)->get();
        } elseif (optional($user)->Role === 'Health Center Staff') {
            $pendingPatientReqs = (clone $patientReqQuery)->with(['patient', 'healthCenter'])->latest('RequestDate')->limit(5)->get();
        }

        $view = 'dashboard';
        if ($user) {
            $roleMap = [
                'Administrator' => 'admin',
                'Health Center Staff' => 'health',
                'Head Pharmacist' => 'pharmacist',
                'Warehouse Staff' => 'warehouse',
                'Accounting Office User' => 'accounting',
                'CMO/GSO/COA User' => 'cmo'
            ];

            $roleSlug = $roleMap[$user->Role] ?? strtolower(explode(' ', $user->Role)[0]);

            if (view()->exists($roleSlug . '.dashboard')) {
                $view = $roleSlug . '.dashboard';
            }
        }

        return view($view, compact('user', 'stats', 'recentRequisitions', 'pendingPOs', 'pendingPatientReqs'))->with('currentPage', 'dashboard');
    }
}
