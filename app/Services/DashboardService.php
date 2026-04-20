<?php

namespace App\Services;

use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\ProcurementOrder;
use App\Models\Procurement\ProcurementOrderItem;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\HealthCenter\HCPatientRequisition;
use App\Models\System\Notification;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

class DashboardService
{
    public function getDashboardData(): array
    {
        $user = Auth::user();
        $role = optional($user)->Role;

        // Base queries
        $requisitionsQuery = Requisition::query();
        $procurementQuery = ProcurementOrder::query();
        $patientReqQuery = HCPatientRequisition::query();

        // Role filtering
        if ($role === 'Health Center Staff' && $user?->HealthCenterID) {
            $requisitionsQuery->where('HealthCenterID', $user->HealthCenterID);
            $procurementQuery->where('HealthCenterID', $user->HealthCenterID);
            $patientReqQuery->where('HealthCenterID', $user->HealthCenterID);
        }

        $isHC = $role === 'Health Center Staff';

        // Low stock
        $lowStockCount = Batch::where('QuantityOnHand', '<', 500)
            ->where('IsLocked', false)
            ->count();

        // Notification logic (kept here, not Blade or controller)
        if ($lowStockCount > 0 && $role !== 'Health Center Staff') {
            Notification::firstOrCreate(
                [
                    'Title' => 'Low Stock Warning',
                    'IsRead' => false,
                    'TargetRole' => 'Head Pharmacist'
                ],
                [
                    'Message' => "There are {$lowStockCount} batches below the safety threshold.",
                    'Link' => route('page.show', ['page' => 'inventory']),
                    'Priority' => 'High'
                ]
            );
        }

        $itemRequestTrends = [
            [
                'item_name' => 'Paracetamol 500mg',
                'request_count' => 18,
            ],
            [
                'item_name' => 'Amoxicillin 250mg',
                'request_count' => 12,
            ],
        ];

        $itemsByInventoryQuantity = Batch::query()
            ->selectRaw('ItemID, SUM(QuantityOnHand) as total_quantity')
            ->groupBy('ItemID')
            ->with('item')
            ->get()
            ->map(function ($ri) {
                return [
                    'item_id' => $ri->ItemID,
                    'item_name' => $ri->item->ItemName ?? 'Unknown Item',
                    'total_quantity' => $ri->total_quantity,
                ];
            });

        $itemsByRequisitionQuantity = RequisitionItem::query()
            ->selectRaw('ItemID, SUM(QuantityRequested) as total_requested')
            ->where('StatusType', 'Approved')
            ->groupBy('ItemID')
            ->with('item')
            ->get()
            ->map(function ($ri) {
                return [
                    'item_id' => $ri->ItemID,
                    'item_name' => $ri->item->ItemName ?? 'Unknown Item',
                    'total_requested' => $ri->total_requested,
                ];
            });

        $itemsByPOQuantity = ProcurementOrderItem::query()
            ->selectRaw('ItemID, SUM(QuantityOrdered) as total_ordered')
            ->whereHas('procurementOrder', function ($query) {
                $query->where('StatusType', 'Approved');
            })
            ->groupBy('ItemID')
            ->with('item')
            ->get()
            ->map(function ($poi) {
                return [
                    'item_id' => $poi->ItemID,
                    'item_name' => $poi->item->ItemName ?? 'Unknown Item',
                    'total_ordered' => $poi->total_ordered,
                ];
            });

        $criticalLowStockItems = $itemsByInventoryQuantity->map(function ($item) use ($itemsByRequisitionQuantity, $itemsByPOQuantity) {
            $requested = $itemsByRequisitionQuantity->where('item_id', $item['item_id'])->first()['total_requested'] ?? 0;
            $ordered = $itemsByPOQuantity->where('item_id', $item['item_id'])->first()['total_ordered'] ?? 0;

            return [
                'item_id' => $item['item_id'],
                'item_name' => $item['item_name'],
                'inventory_on_hand' => $item['total_quantity'],
                'required_quantity' => $requested,
                'incoming_po_quantity' => $ordered,
            ];
        })->filter(function ($item) {
            return $item['inventory_on_hand'] <= $item['required_quantity'];
        })->values();

        $topSeasonalItems = RequisitionItem::query()
            ->selectRaw('ItemID, COUNT(*) as total_requests')
            ->groupBy('ItemID')
            ->orderByDesc('total_requests')
            ->take(5)
            ->pluck('ItemID');

        $selectedTrendYear = request('trend_year', now()->year);

        $availableTrendYears = Requisition::query()
            ->selectRaw('YEAR(CreatedAt) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $seasonalDemandMonths = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        $colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'];

        $seasonalDemandDatasets = [];

        foreach ($topSeasonalItems as $index => $itemId) {
            $item = Item::find($itemId);

            $monthlyCounts = [];

            foreach (range(1, 12) as $month) {
                $count = RequisitionItem::query()
                    ->where('ItemID', $itemId)
                    ->whereHas('requisition', function ($query) use ($month, $selectedTrendYear) {
                        $query->whereYear('CreatedAt', $selectedTrendYear)
                            ->whereMonth('CreatedAt', $month);
                    })
                    ->count();

                $monthlyCounts[] = $count;
            }

            $seasonalDemandDatasets[] = [
                'label' => $item->ItemName ?? 'Unknown Item',
                'color' => $colors[$index % count($colors)],
                'data' => $monthlyCounts,
            ];
        }

        return [
            'user' => $user,
            'role' => $role,

            // UI helpers (NO Blade logic anymore)
            'ui' => $this->getUiConfig($role),

            // stats
            'stats' => [
                'pending_reqs' => (clone $requisitionsQuery)->where('StatusType', 'Pending')->count(),
                'completed_reqs' => (clone $requisitionsQuery)->where('StatusType', 'Completed')->count(),
                'pending_pos' => $isHC ? 0 : (clone $procurementQuery)->where('StatusType', 'Pending')->count(),
                'completed_pos' => $isHC ? 0 : (clone $procurementQuery)->where('StatusType', 'Completed')->count(),
                'low_stock' => $isHC ? 0 : $lowStockCount,
                'pending_patient_reqs' => (clone $patientReqQuery)->where('StatusType', 'Pending')->count(),
            ],

            'seasonalDemandMonths' => $seasonalDemandMonths,
            'seasonalDemandDatasets' => $seasonalDemandDatasets,
            'availableTrendYears' => $availableTrendYears,
            'selectedTrendYear' => $selectedTrendYear,
            'criticalLowStockItems' => $criticalLowStockItems,
            'itemRequestTrends' => $itemRequestTrends,

            // tables
            'recentRequisitions' => $requisitionsQuery
                ->with('healthCenter')
                ->latest('RequestDate')
                ->limit(5)
                ->get(),

            'pendingPOs' => !$isHC && in_array($role, [
                'Administrator',
                'Head Pharmacist',
                'Warehouse Staff',
                'Accounting Office User'
            ])
                ? (clone $procurementQuery)
                    ->with(['supplier', 'healthCenter'])
                    ->where('StatusType', 'Pending')
                    ->latest('PODate')
                    ->limit(5)
                    ->get()
                : collect(),

            'pendingPatientReqs' => $this->getPatientReqs($role, $patientReqQuery),
        ];
    }

    private function getPatientReqs(string|null $role, $query)
    {
        if (in_array($role, ['Administrator', 'Head Pharmacist'])) {
            return (clone $query)
                ->with(['patient', 'healthCenter'])
                ->where('StatusType', 'Pending')
                ->latest('RequestDate')
                ->limit(5)
                ->get();
        }

        if ($role === 'Health Center Staff') {
            return (clone $query)
                ->with(['patient', 'healthCenter'])
                ->latest('RequestDate')
                ->limit(5)
                ->get();
        }

        return collect();
    }

    private function getUiConfig(?string $role): array
    {
        return [
            'userName' => trim(optional(Auth::user())->FName . ' ' . optional(Auth::user())->LName) ?: 'User',
            'activeRole' => $role ?? 'N/A',

            'badgeClass' => match ($role) {
                'Administrator' => 'bg-purple-500/20 border-purple-500/30 text-purple-300',
                'Health Center Staff' => 'bg-cyan-500/20 border-cyan-500/30 text-cyan-300',
                'Head Pharmacist' => 'bg-indigo-500/20 border-indigo-500/30 text-indigo-300',
                'Warehouse Staff' => 'bg-amber-500/20 border-amber-500/30 text-amber-300',
                'Accounting Office User' => 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300',
                'CMO/GSO/COA User' => 'bg-teal-500/20 border-teal-500/30 text-teal-300',
                default => 'bg-slate-500/20 border-slate-500/30 text-slate-300'
            },

            'pulseClass' => match ($role) {
                'Administrator' => 'bg-purple-400',
                'Health Center Staff' => 'bg-cyan-400',
                'Head Pharmacist' => 'bg-indigo-400',
                'Warehouse Staff' => 'bg-amber-400',
                'Accounting Office User' => 'bg-emerald-400',
                'CMO/GSO/COA User' => 'bg-teal-400',
                default => 'bg-slate-400'
            },

            'btnClass' => match ($role) {
                'Administrator' => 'bg-purple-600 hover:bg-purple-700 shadow-purple-500/30',
                'Health Center Staff' => 'bg-cyan-600 hover:bg-cyan-700 shadow-cyan-500/30',
                'Head Pharmacist' => 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/30',
                'Warehouse Staff' => 'bg-amber-600 hover:bg-amber-700 shadow-amber-500/30',
                'Accounting Office User' => 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30',
                'CMO/GSO/COA User' => 'bg-teal-600 hover:bg-teal-700 shadow-teal-500/30',
                default => 'bg-slate-600 hover:bg-slate-700 shadow-slate-500/30'
            },

            'blur1' => match ($role) {
                'Administrator' => 'bg-purple-500/20',
                'Health Center Staff' => 'bg-cyan-500/20',
                'Head Pharmacist' => 'bg-indigo-500/20',
                'Warehouse Staff' => 'bg-amber-500/20',
                'Accounting Office User' => 'bg-emerald-500/20',
                'CMO/GSO/COA User' => 'bg-teal-500/20',
                default => 'bg-slate-500/20'
            },

            'blur2' => match ($role) {
                'Administrator' => 'bg-fuchsia-500/20',
                'Health Center Staff' => 'bg-blue-500/20',
                'Head Pharmacist' => 'bg-purple-500/20',
                'Warehouse Staff' => 'bg-orange-500/20',
                'Accounting Office User' => 'bg-green-500/20',
                'CMO/GSO/COA User' => 'bg-cyan-500/20',
                default => 'bg-slate-600/20'
            },
        ];
    }
}
