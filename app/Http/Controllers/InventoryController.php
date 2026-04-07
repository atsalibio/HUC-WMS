<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory\Item;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $inventory = \App\Models\Inventory\Batch::where('QuantityOnHand', '>', 0)->get();

        $batchesByItem = [];
        foreach ($inventory as $batch) {
            $batchesByItem[$batch->ItemID][] = $batch;
        }

        $aggregatedInventory = [];
        foreach ($items as $item) {
            $itemId = $item->ItemID;
            $totalQty = 0;
            $nextExpiry = null;
            $itemBatches = $batchesByItem[$itemId] ?? [];

            foreach ($itemBatches as $b) {
                $totalQty += $b->QuantityOnHand;
                if ($nextExpiry === null || strtotime($b->ExpiryDate) < strtotime($nextExpiry)) {
                    $nextExpiry = $b->ExpiryDate;
                }
            }

            $aggregatedInventory[] = [
                'ItemID'      => $itemId,
                'ItemName'    => $item->ItemName,
                'Brand'       => $item->Brand ?? '',
                'DosageUnit'  => $item->DosageUnit ?? '',
                'Category'    => $item->ItemType ?? 'N/A',
                'Unit'        => $item->UnitOfMeasure ?? 'N/A',
                'TotalQuantity' => $totalQty,
                'NextExpiry'  => $nextExpiry
            ];
        }

        return view('pages.inventory', [
            'aggregatedInventory' => $aggregatedInventory,
            'batchesByItem' => $batchesByItem,
            'currentPage' => 'inventory'
        ]);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'ItemName' => 'required|string|max:255',
            'Brand' => 'nullable|string|max:255',
            'DosageUnit' => 'nullable|string|max:100',
            'ItemType' => 'required|string',
            'UnitOfMeasure' => 'required|string',
        ]);

        Item::create([
            'ItemName' => $request->ItemName,
            'Brand' => $request->Brand,
            'DosageUnit' => $request->DosageUnit,
            'ItemType' => $request->ItemType,
            'UnitOfMeasure' => $request->UnitOfMeasure,
        ]);

        return response()->json(['success' => true, 'message' => 'Item registry expanded successfully.']);
    }

    public function dpriIndex()
    {
        return view('pages.dpri_import');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.ItemName' => 'required|string',
            'items.*.ItemType' => 'required|string',
            'items.*.UnitOfMeasure' => 'nullable|string',
        ]);

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($request->items as $itemData) {
            try {
                Item::updateOrCreate(
                    ['ItemName' => $itemData['ItemName']],
                    [
                        'ItemType' => $itemData['ItemType'],
                        'UnitOfMeasure' => $itemData['UnitOfMeasure'] ?? 'Unit',
                    ]
                );
                $importedCount++;
            } catch (\Exception $e) {
                $skippedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'count' => $importedCount,
            'skipped' => $skippedCount
        ]);
    }
}
