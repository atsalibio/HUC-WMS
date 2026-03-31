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
        $lowStockCount = Batch::where('QuantityOnHand', '<', 500)->count();
        if ($lowStockCount > 0) {
            Notification::firstOrCreate(
                ['title' => 'Low Stock Warning', 'is_read' => false, 'target_role' => 'Head Pharmacist'],
                [
                    'message' => "There are {$lowStockCount} batches below the safety threshold.",
                    'link' => route('page.show', ['page' => 'inventory']),
                    'priority' => 'High'
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

        $stats = [
            'pending_reqs' => (clone $requisitionsQuery)->where('StatusType', 'Pending')->count(),
            'pending_pos' => (clone $procurementQuery)->where('StatusType', 'Pending')->count(),
            'low_stock' => Batch::where('QuantityOnHand', '<', 500)->count(),
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
        }

        $view = 'dashboard';
        if ($user) {
            $roleSlug = strtolower(explode(' ', $user->Role)[0]);
            if (view()->exists($roleSlug . '.dashboard')) {
                $view = $roleSlug . '.dashboard';
            }
        }

        return view($view, compact('user', 'stats', 'recentRequisitions', 'pendingPOs', 'pendingPatientReqs'))->with('currentPage', 'dashboard');
    }
}