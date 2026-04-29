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
        Schema::table('Notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('Notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('NotificationID');
            }
            if (!Schema::hasColumn('Notifications', 'target_role')) {
                $table->string('target_role')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('Notifications', 'title')) {
                $table->string('title')->after('target_role');
            }
            if (!Schema::hasColumn('Notifications', 'message')) {
                $table->text('message')->after('title');
            }
            if (!Schema::hasColumn('Notifications', 'link')) {
                $table->string('link')->nullable()->after('message');
            }
            if (!Schema::hasColumn('Notifications', 'priority')) {
                $table->string('priority')->default('Normal')->after('link');
            }
            if (!Schema::hasColumn('Notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Notifications', function (Blueprint $table) {
            //
        });
    }
};
