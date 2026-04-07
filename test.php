<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Exact same query as PageController
$orders = \App\Models\Procurement\ProcurementOrder::where('StatusType', 'Approved')
    ->with(['supplier', 'items.item'])
    ->get();

echo "Pending approved orders: " . $orders->count() . "\n";

if ($orders->isEmpty()) {
    echo "❌ NO APPROVED ORDERS - check StatusType in DB\n";
    $all = \App\Models\Procurement\ProcurementOrder::all(['POID','PONumber','StatusType']);
    foreach ($all as $o) {
        echo "  POID={$o->POID} PONumber={$o->PONumber} StatusType={$o->StatusType}\n";
    }
} else {
    $order = $orders->first();
    echo "✅ Order found: POID={$order->POID} PONumber={$order->PONumber}\n";
    echo "   Items count: " . $order->items->count() . "\n";
    
    foreach ($order->items as $item) {
        echo "   - ItemID={$item->ItemID} | item.ItemName=" . ($item->item->ItemName ?? 'NULL') . "\n";
        if (is_null($item->item)) {
            echo "     ❌ item relation is NULL! Is ItemID valid?\n";
        }
    }
    
    // Simulate what Alpine.js accesses:
    $arr = $order->toArray();
    echo "\n--- Keys in order JSON ---\n";
    echo implode(', ', array_keys($arr)) . "\n";
    
    echo "\n--- Items key in JSON ---\n";
    foreach ($arr['items'] ?? [] as $i) {
        echo "  item key present: " . (isset($i['item']) ? 'YES' : 'NO') . "\n";
        echo "  item.ItemName: " . ($i['item']['ItemName'] ?? 'MISSING') . "\n";
    }
}

// Check the issuance page data too
echo "\n--- Issuance page: approved requisitions ---\n";
$reqs = \App\Models\Requisition\Requisition::whereIn('StatusType', ['Approved', 'Partial'])
    ->with(['healthCenter', 'items.item'])
    ->get();
    
echo "Approved requisitions: " . $reqs->count() . "\n";
if ($reqs->isEmpty()) {
    $all = \App\Models\Requisition\Requisition::all(['RequisitionID','RequisitionNumber','StatusType']);
    foreach ($all as $r) {
        echo "  ReqID={$r->RequisitionID} Status={$r->StatusType}\n";
    }
} else {
    foreach ($reqs as $req) {
        echo "✅ Req: {$req->RequisitionID} Status={$req->StatusType} HC=" . ($req->healthCenter->Name ?? 'NULL') . "\n";
        foreach ($req->items as $item) {
            echo "   item.ItemName=" . ($item->item->ItemName ?? 'NULL') . "\n";
        }
    }
}


// Simulate what the browser does: decode HTML entities then JSON.parse
// HTML attribute stores: data-order="...JSON_HEX_encoded..."
// Browser reads dataset.order and gets the decoded string
// We simulate that by html_entity_decode (what the browser does automatically)
$decoded_by_browser = html_entity_decode($encoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Now try to JSON decode (simulates JSON.parse in JS)
$result = json_decode($decoded_by_browser);

$issues = [];

if (json_last_error() !== JSON_ERROR_NONE) {
    $issues[] = "JSON parse FAIL: " . json_last_error_msg();
} else {
    echo "✅ JSON.parse() would SUCCESS - POID: " . $result->POID . "\n";
    echo "   Items count: " . count($result->items) . "\n";
    if (!empty($result->items)) {
        $first = $result->items[0];
        echo "   First item: " . ($first->item->ItemName ?? 'NO ITEM NAME') . "\n";
    }
}

echo "\n--- Checking for single quotes that could break JS ---\n";
if (strpos($encoded, "'") !== false) {
    $issues[] = "SINGLE QUOTES in encoded JSON - will break JS string";
} else {
    echo "✅ No single quotes in encoded output\n";
}

echo "\n--- Checking for unescaped double quotes ---\n";
if (preg_match('/"[^"]*"[^"]*"/', $encoded)) {
    $issues[] = "Possible unescaped double quotes";
} else {
    echo "✅ Double quotes properly escaped with \\u0022\n";
}

if (!empty($issues)) {
    echo "\n❌ ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}

echo "\n--- First 200 chars of encoded value ---\n";
echo substr($encoded, 0, 200) . "\n";




