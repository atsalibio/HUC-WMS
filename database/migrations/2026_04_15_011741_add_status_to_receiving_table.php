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
        Schema::table('Receiving', function (Blueprint $table) {
            $table->string('StatusType')->default('Received')->after('UserID');
        });
    }

    public function down(): void
    {
        Schema::table('Receiving', function (Blueprint $table) {
            $table->dropColumn('StatusType');
        });
    }
};
