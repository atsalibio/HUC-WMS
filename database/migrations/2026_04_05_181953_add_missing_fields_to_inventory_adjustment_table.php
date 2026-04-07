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
        Schema::table('InventoryAdjustment', function (Blueprint $table) {
            if (!Schema::hasColumn('InventoryAdjustment', 'EvidencePath')) {
                $table->string('EvidencePath')->nullable()->after('Reason');
            }
            if (!Schema::hasColumn('InventoryAdjustment', 'RequisitionID')) {
                $table->unsignedBigInteger('RequisitionID')->nullable()->after('BatchID');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('InventoryAdjustment', function (Blueprint $table) {
            $table->dropColumn(['EvidencePath', 'RequisitionID']);
        });
    }
};
