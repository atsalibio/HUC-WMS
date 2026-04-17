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
        Schema::table('ProcurementOrderItem', function (Blueprint $table) {
            if (!Schema::hasColumn('ProcurementOrderItem', 'BatchID')) {
                $table->string('BatchID')->nullable()->after('ItemID');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ProcurementOrderItem', function (Blueprint $table) {
            $table->dropColumn('BatchID');
        });
    }
};
