<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = ['HCInventoryBatch', 'HCPatientRequisition', 'HCPatientRequisitionItem', 'notifications'];

foreach ($tables as $table) {
    echo "--- TABLE: {$table} ---\n";
    if (Schema::hasTable($table)) {
        $columns = Schema::getColumnListing($table);
        echo "Columns: " . implode(', ', $columns) . "\n";
    } else {
        echo "Table DOES NOT exist.\n";
    }
}
