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
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id('AdjustmentID');
            $table->string('BatchID')->nullable();
            $table->unsignedBigInteger('RequisitionID')->nullable();
            $table->string('AdjustmentType'); // 'Disposal', 'Return', 'Correction'
            $table->integer('QuantityAdjusted');
            $table->string('Reason');
            $table->text('Remarks')->nullable();
            $table->string('EvidencePath')->nullable();
            $table->unsignedBigInteger('AdjustedBy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
