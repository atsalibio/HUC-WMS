<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Fix receivings table columns if missing
Schema::table('receivings', function(Blueprint $table) {
    if (!Schema::hasColumn('receivings', 'POID')) { $table->string('POID')->nullable(); }
    if (!Schema::hasColumn('receivings', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
    if (!Schema::hasColumn('receivings', 'ReceivedDate')) { $table->dateTime('ReceivedDate')->nullable(); }
});

// Fix receiving_items table columns if missing
Schema::table('receiving_items', function(Blueprint $table) {
    if (!Schema::hasColumn('receiving_items', 'ReceivingID')) { $table->unsignedBigInteger('ReceivingID')->nullable(); }
    if (!Schema::hasColumn('receiving_items', 'BatchID')) { $table->unsignedBigInteger('BatchID')->nullable(); }
    if (!Schema::hasColumn('receiving_items', 'QuantityReceived')) { $table->float('QuantityReceived')->default(0); }
});

echo "Schema updated!\n";
