<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('HCInventoryBatch', function (Blueprint $table) {
            if (!Schema::hasColumn('HCInventoryBatch', 'LotNumber')) {
                $table->string('LotNumber')->nullable()->after('BatchID');
            }
            if (!Schema::hasColumn('HCInventoryBatch', 'UnitCost')) {
                $table->float('UnitCost')->default(0)->after('QuantityOnHand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('HCInventoryBatch', function (Blueprint $table) {
            $table->dropColumn(['LotNumber', 'UnitCost']);
        });
    }
};
