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
        Schema::table('RequisitionItem', function (Blueprint $table) {
            if (!Schema::hasColumn('RequisitionItem', 'ItemStatus')) {
                $table->string('ItemStatus')->default('Pending')->nullable();
            }
            if (!Schema::hasColumn('RequisitionItem', 'Remarks')) {
                $table->text('Remarks')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('RequisitionItem', function (Blueprint $table) {
            $table->dropColumn(['ItemStatus', 'Remarks']);
        });
    }
};
