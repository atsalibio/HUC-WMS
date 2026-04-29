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
            if (!Schema::hasColumn('InventoryAdjustment', 'AdjustmentType')) {
                $table->string('AdjustmentType')->after('UserID'); // 'Disposal', 'Return', 'Correction' etc.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('InventoryAdjustment', function (Blueprint $table) {
            $table->dropColumn('AdjustmentType');
        });
    }
};
