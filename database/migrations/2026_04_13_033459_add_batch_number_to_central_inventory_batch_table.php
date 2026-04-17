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
            if (!Schema::hasColumn('CentralInventoryBatch', 'BatchNumber')) {
                $table->string('BatchNumber')->nullable()->after('LotNumber');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CentralInventoryBatch', function (Blueprint $table) {
            $table->dropColumn('BatchNumber');
        });
    }
};
