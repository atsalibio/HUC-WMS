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
        Schema::table('CentralInventoryBatch', function (Blueprint $table) {
            if (!Schema::hasColumn('CentralInventoryBatch', 'IsLocked')) {
                $table->boolean('IsLocked')->default(false)->after('QuantityReleased');
            }
        });

        Schema::table('ProcurementOrderItem', function (Blueprint $table) {
            if (!Schema::hasColumn('ProcurementOrderItem', 'LotNumber')) {
                $table->string('LotNumber')->nullable()->after('ItemID');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CentralInventoryBatch', function (Blueprint $table) {
            $table->dropColumn('IsLocked');
        });

        Schema::table('ProcurementOrderItem', function (Blueprint $table) {
            $table->dropColumn('LotNumber');
        });
    }
};
