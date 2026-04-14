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
        if (!Schema::hasTable('RequisitionApprovalLog')) {
            Schema::create('RequisitionApprovalLog', function (Blueprint $table) {
                $table->id('ApprovalLogID');
                $table->integer('RequisitionID');
                $table->integer('UserID');
                $table->string('Decision');
                $table->datetime('DecisionDate');
                $table->string('Remarks')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RequisitionApprovalLog');
    }
};
