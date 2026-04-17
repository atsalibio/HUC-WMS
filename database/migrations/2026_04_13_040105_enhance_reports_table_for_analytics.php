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
        Schema::table('report', function (Blueprint $table) {
            if (!Schema::hasColumn('report', 'Data')) {
                $table->json('Data')->nullable()->after('GeneratedForOffice');
            }
            if (!Schema::hasColumn('report', 'ReferenceID')) {
                $table->string('ReferenceID')->nullable()->after('Data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report', function (Blueprint $table) {
            $table->dropColumn(['Data', 'ReferenceID']);
        });
    }
};
