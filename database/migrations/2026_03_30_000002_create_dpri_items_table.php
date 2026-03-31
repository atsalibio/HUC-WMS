<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dpri_items')) {
            Schema::create('dpri_items', function (Blueprint $table) {
                $table->id('DPRIItemID');
                $table->string('ItemName', 200);
                $table->string('Brand', 150)->nullable();
                $table->string('UnitOfMeasure', 50)->nullable();
                $table->string('DosageUnit', 100)->nullable();
                $table->decimal('ReferencePrice', 15, 2)->default(0);
                $table->string('Category', 100)->nullable();
                $table->year('ReferenceYear')->nullable();
                $table->timestamps();
                
                $table->index('ItemName');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dpri_items');
    }
};
