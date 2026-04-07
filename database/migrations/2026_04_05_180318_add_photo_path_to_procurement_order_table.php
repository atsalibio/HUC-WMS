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
        Schema::table('ProcurementOrder', function (Blueprint $table) {
            $table->string('PhotoPath')->nullable()->after('DocumentType');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ProcurementOrder', function (Blueprint $table) {
            $table->dropColumn('PhotoPath');
        });
    }
};
