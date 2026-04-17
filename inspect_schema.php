<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'InventoryAdjustment';
echo "Columns for $table:\n";
print_r(Schema::getColumnListing($table));

$table2 = 'inventory_adjustments';
echo "\nColumns for $table2:\n";
print_r(Schema::getColumnListing($table2));

$table3 = 'notifications';
echo "\nColumns for $table3:\n";
print_r(Schema::getColumnListing($table3));
