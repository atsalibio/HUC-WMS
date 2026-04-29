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
        if (!Schema::hasTable('InventoryCorrection')) {
            Schema::create('InventoryCorrection', function (Blueprint $table) {
                $table->id('CorrectionID');
                $table->unsignedInteger('WarehouseID')->nullable();
                $table->unsignedInteger('HealthCenterID')->nullable();
                $table->unsignedBigInteger('UserID')->nullable();
                $table->string('BatchID')->nullable();
                $table->unsignedInteger('ItemID')->nullable();
                $table->integer('QuantityBefore')->nullable();
                $table->integer('QuantityCorrected')->nullable();
                $table->string('Reason')->nullable();
                $table->string('EvidencePath')->nullable();
                $table->string('StatusType')->default('Pending');
                $table->dateTime('CorrectionDate')->nullable();

                // Foreign Keys
                $table->foreign('UserID')->references('UserID')->on('Users')->onDelete('cascade');
                $table->foreign('WarehouseID')->references('WarehouseID')->on('Warehouse')->onDelete('cascade');
                $table->foreign('HealthCenterID')->references('HealthCenterID')->on('HealthCenters')->onDelete('cascade');
                $table->foreign('BatchID')->references('BatchID')->on('CentralInventoryBatch')->onDelete('cascade');
                $table->foreign('ItemID')->references('ItemID')->on('Item')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('InventoryCorrection');
    }
};
