<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\ProcurementOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $completedPOs = ProcurementOrder::whereIn('StatusType', ['Completed', 'Approved'])->get();
        
        // Mocking history for now if table doesn't exist, else fetch from DB
        $history = []; 
        try {
            $history = DB::table('reports')->select('*')
                ->orderBy('GeneratedDate', 'desc')
                ->get();
        } catch (\Exception $e) {
            // Table might not exist yet
        }

        return view('pages.reports', compact('items', 'completedPOs', 'history'));
    }

    public function generate(Request $request)
    {
        $type = $request->reportType;
        $office = $request->forOffice;
        $reportData = [];

        if ($type === 'inventory_valuation') {
            $totalValuation = Batch::where('QuantityOnHand', '>', 0)
                ->get()
                ->sum(function($b) {
                    return $b->QuantityOnHand * ($b->UnitCost ?? 0);
                });
                
            $reportData = [
                'TotalValuation' => $totalValuation,
                'TotalItems' => Item::count(),
                'ActiveBatches' => Batch::where('QuantityOnHand', '>', 0)->count()
            ];
        }

        // Simulating the report object for the history list
        $newReport = [
            'ReportID' => 'RPT-' . strtoupper(uniqid()),
            'ReportType' => ucwords(str_replace('_', ' ', $type)),
            'GeneratedForOffice' => ucwords($office),
            'GeneratedByFullName' => Auth::user()->FName . ' ' . Auth::user()->LName,
            'GeneratedDate' => now()->toDateTimeString(),
            'Data' => json_encode($reportData)
        ];

        // In a real app, we'd save this to a 'reports' table
        try {
             DB::table('reports')->insert($newReport);
        } catch (\Exception $e) {
            // Silently fail if table missing, just return the obj to frontend
        }

        return response()->json([
            'success' => true,
            'report' => $newReport
        ]);
    }
}
