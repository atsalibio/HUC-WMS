<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Item;
use App\Models\Procurement\ProcurementOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\System\Report;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('ItemName')->get();
        // Get POs that have some receiving history
        $completedPOs = ProcurementOrder::whereIn('StatusType', ['Completed', 'Approved'])
            ->orderBy('PODate', 'desc')
            ->get();

        $history = Report::select('report.*', 'users.FName', 'users.LName')
            ->join('users', 'report.UserID', '=', 'users.UserID')
            ->orderBy('GeneratedDate', 'desc')
            ->get()
            ->map(function($rpt) {
                // Manually setting properties for the frontend expectation
                $rpt->GeneratedByFullName = $rpt->FName . ' ' . $rpt->LName;
                return $rpt;
            });

        return view('pages.reports', compact('items', 'completedPOs', 'history'));
    }

    public function generate(Request $request)
    {
        $type = $request->reportType;
        $office = $request->forOffice;
        $reportData = [];
        $refId = null;

        try {
            if ($type === 'inventory_valuation') {
                $batches = Batch::with('item')
                    ->where('QuantityOnHand', '>', 0)
                    ->get();

                $categoryBreakdown = $batches->groupBy(fn($b) => $b->item->Category ?? 'Others')
                    ->map(fn($group) => [
                        'count' => $group->count(),
                        'value' => $group->sum(fn($b) => $b->QuantityOnHand * ($b->UnitCost ?? 0))
                    ]);

                $reportData = [
                    'TotalValuation' => $batches->sum(fn($b) => $b->QuantityOnHand * ($b->UnitCost ?? 0)),
                    'TotalActiveItems' => $batches->pluck('ItemID')->unique()->count(),
                    'CategoryBreakdown' => $categoryBreakdown,
                    'GeneratedAt' => now()->toDateTimeString()
                ];
            }
            else if ($type === 'receipt_confirmation') {
                $poId = $request->poId;
                $refId = $poId;
                $po = ProcurementOrder::with('items.item')->where('POID', $poId)->first();

                if (!$po) return response()->json(['success' => false, 'message' => 'PO not found']);

                $receivings = DB::table('receiving')
                    ->where('POID', $poId)
                    ->join('receivingitem', 'receiving.ReceivingID', '=', 'receivingitem.ReceivingID')
                    ->select('receivingitem.*', 'receiving.ReceivedDate', 'receivingitem.ItemID')
                    ->get();

                $itemVerification = $po->items->map(function($poItem) use ($receivings) {
                    $received = $receivings->where('ItemID', $poItem->ItemID)->sum('QuantityReceived');
                    return [
                        'ItemName' => $poItem->item->ItemName,
                        'Ordered' => $poItem->QuantityOrdered,
                        'Received' => $received,
                        'Variance' => $poItem->QuantityOrdered - $received,
                    ];
                });

                $reportData = [
                    'PONumber' => $po->PONumber,
                    'Supplier' => $po->SupplierName,
                    'Items' => $itemVerification,
                    'TotalItems' => $po->items->count()
                ];
            }
            else if ($type === 'stock_card_ledger') {
                $itemId = $request->itemId;
                $refId = $itemId;
                $item = Item::where('ItemID', $itemId)->first();

                if (!$item) return response()->json(['success' => false, 'message' => 'Item not found']);

                // 1. Incoming (Receivings)
                $incomings = DB::table('ReceivingItem')
                    ->join('Receiving', 'ReceivingItem.ReceivingID', '=', 'Receiving.ReceivingID')
                    ->join('CentralInventoryBatch', 'ReceivingItem.BatchID', '=', 'CentralInventoryBatch.BatchID')
                    ->where('CentralInventoryBatch.ItemID', $itemId)
                    ->select(
                        'Receiving.ReceivedDate as date',
                        DB::raw("'Receiving' as type"),
                        DB::raw("Receiving.ReceivingID as ref"),
                        'ReceivingItem.QuantityReceived as qty_in',
                        DB::raw("0 as qty_out")
                    );

                // 2. Outgoing (Issuances)
                $outgoings = DB::table('IssuanceItem')
                    ->join('Issuance', 'IssuanceItem.IssuanceID', '=', 'Issuance.IssuanceID')
                    ->join('CentralInventoryBatch', 'IssuanceItem.BatchID', '=', 'CentralInventoryBatch.BatchID')
                    ->where('CentralInventoryBatch.ItemID', $itemId)
                    ->select(
                        'Issuance.IssueDate as date',
                        DB::raw("'Issuance' as type"),
                        DB::raw("Issuance.IssuanceID as ref"),
                        DB::raw("0 as qty_in"),
                        'IssuanceItem.QuantityIssued as qty_out'
                    );

                // 3. Adjustments
                $adjustments = DB::table('InventoryAdjustment')
                    ->join('CentralInventoryBatch', 'InventoryAdjustment.BatchID', '=', 'CentralInventoryBatch.BatchID')
                    ->where('CentralInventoryBatch.ItemID', $itemId)
                    ->select(
                        'InventoryAdjustment.AdjustmentDate as date',
                        DB::raw("'Adjustment' as type"),
                        DB::raw("InventoryAdjustment.AdjustmentID as ref"),
                        DB::raw("CASE WHEN InventoryAdjustment.AdjustmentQuantity > 0 THEN InventoryAdjustment.AdjustmentQuantity ELSE 0 END as qty_in"),
                        DB::raw("CASE WHEN InventoryAdjustment.AdjustmentQuantity < 0 THEN ABS(InventoryAdjustment.AdjustmentQuantity) ELSE 0 END as qty_out")
                    );

                $ledgerRows = $incomings->union($outgoings)->union($adjustments)
                    ->orderBy('date', 'asc')
                    ->get();

                $runningBalance = 0;
                $ledger = $ledgerRows->map(function($row) use (&$runningBalance) {
                    $runningBalance += ($row->qty_in - $row->qty_out);
                    return [
                        'date' => $row->date,
                        'type' => $row->type,
                        'ref' => $row->ref,
                        'in' => $row->qty_in,
                        'out' => $row->qty_out,
                        'balance' => $runningBalance
                    ];
                });

                $reportData = [
                    'ItemName' => $item->ItemName,
                    'Unit' => $item->UnitOfMeasure,
                    'Ledger' => $ledger,
                    'CurrentBalance' => $runningBalance
                ];
            }

            $newReport = Report::create([
                'UserID' => Auth::user()->UserID,
                'ReportType' => ucwords(str_replace('_', ' ', $type)),
                'GeneratedForOffice' => $office,
                'GeneratedDate' => now(),
                'ReferenceID' => (int)$refId,
                'Data' => $reportData
            ]);

            // 通知 Trigger: Notify Generator
            NotificationController::create(
                "Report Archived",
                "The " . ucwords(str_replace('_', ' ', $type)) . " report has been successfully archived.",
                "/reports",
                null,
                Auth::user()->UserID
            );

            return response()->json([
                'success' => true,
                'report' => [
                    'ReportID' => $newReport->ReportID,
                    'ReportType' => ucwords($newReport->ReportType),
                    'GeneratedForOffice' => ucwords($newReport->GeneratedForOffice),
                    'GeneratedByFullName' => Auth::user()->FName . ' ' . Auth::user()->LName,
                    'GeneratedDate' => $newReport->GeneratedDate->toDateTimeString(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Synthesis Error: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $report = Report::with('user')->where('ReportID', $id)->first();
        if (!$report) return response()->json(['success' => false, 'message' => 'Report not found']);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    public function exportPdf($id)
    {
        $report = Report::with('user')->where('ReportID', $id)->first();
        if (!$report) abort(404);

        $pdf = Pdf::loadView('pdf.report_pdf', compact('report'));
        return $pdf->download($report->ReportID . '.pdf');
    }
}
