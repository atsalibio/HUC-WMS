<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Requisitions
        if (Schema::hasTable('requisitions')) {
            Schema::table('requisitions', function (Blueprint $table) {
                if (!Schema::hasColumn('requisitions', 'HealthCenterID')) { $table->unsignedBigInteger('HealthCenterID')->nullable()->after('id'); }
                if (!Schema::hasColumn('requisitions', 'HealthCenterName')) { $table->string('HealthCenterName')->nullable(); }
                if (!Schema::hasColumn('requisitions', 'HealthCenterAddress')) { $table->string('HealthCenterAddress')->nullable(); }
                if (!Schema::hasColumn('requisitions', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
                if (!Schema::hasColumn('requisitions', 'RequestDate')) { $table->dateTime('RequestDate')->nullable(); }
                if (!Schema::hasColumn('requisitions', 'StatusType')) { $table->string('StatusType')->default('Pending'); }
            });
        }

        // 2. Requisition Items
        if (Schema::hasTable('requisition_items')) {
            Schema::table('requisition_items', function (Blueprint $table) {
                if (!Schema::hasColumn('requisition_items', 'RequisitionID')) { $table->unsignedBigInteger('RequisitionID')->nullable()->after('id'); }
                if (!Schema::hasColumn('requisition_items', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable(); }
                if (!Schema::hasColumn('requisition_items', 'QuantityRequested')) { $table->float('QuantityRequested')->default(0); }
                if (!Schema::hasColumn('requisition_items', 'QuantityReceived')) { $table->float('QuantityReceived')->default(0); }
                if (!Schema::hasColumn('requisition_items', 'QuantityIssued')) { $table->float('QuantityIssued')->default(0); }
            });
        }

        // 3. Batches
        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                if (!Schema::hasColumn('batches', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable()->after('id'); }
                if (!Schema::hasColumn('batches', 'QuantityOnHand')) { $table->float('QuantityOnHand')->default(0); }
                if (!Schema::hasColumn('batches', 'QuantityReleased')) { $table->float('QuantityReleased')->default(0); }
                if (!Schema::hasColumn('batches', 'UnitCost')) { $table->float('UnitCost')->default(0); }
                if (!Schema::hasColumn('batches', 'ExpiryDate')) { $table->date('ExpiryDate')->nullable(); }
            });
        }

        // 4. HC Inventory Batches
        if (!Schema::hasTable('hc_inventory_batches')) {
            Schema::create('hc_inventory_batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('HealthCenterID');
                $table->unsignedBigInteger('ItemID');
                $table->unsignedBigInteger('BatchID');
                $table->date('ExpiryDate')->nullable();
                $table->float('QuantityOnHand')->default(0);
                $table->float('UnitCost')->default(0);
                $table->timestamps();
            });
        }

        // 5. Issuances
        if (Schema::hasTable('issuances')) {
            Schema::table('issuances', function (Blueprint $table) {
                if (!Schema::hasColumn('issuances', 'RequisitionID')) { $table->unsignedBigInteger('RequisitionID')->nullable()->after('id'); }
                if (!Schema::hasColumn('issuances', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
                if (!Schema::hasColumn('issuances', 'IssueDate')) { $table->dateTime('IssueDate')->nullable(); }
                if (!Schema::hasColumn('issuances', 'StatusType')) { $table->string('StatusType')->default('Pending'); }
            });
        }

        // 6. Issuance Items
        if (Schema::hasTable('issuance_items')) {
            Schema::table('issuance_items', function (Blueprint $table) {
                if (!Schema::hasColumn('issuance_items', 'IssuanceID')) { $table->unsignedBigInteger('IssuanceID')->nullable()->after('id'); }
                if (!Schema::hasColumn('issuance_items', 'BatchID')) { $table->unsignedBigInteger('BatchID')->nullable(); }
                if (!Schema::hasColumn('issuance_items', 'RequisitionItemID')) { $table->unsignedBigInteger('RequisitionItemID')->nullable(); }
                if (!Schema::hasColumn('issuance_items', 'QuantityIssued')) { $table->float('QuantityIssued')->default(0); }
            });
        }

        // 7. Procurement Orders
        if (Schema::hasTable('procurement_orders')) {
            Schema::table('procurement_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('procurement_orders', 'StatusType')) { $table->string('StatusType')->default('Pending')->after('id'); }
            });
        }

        // 8. Receivings
        if (Schema::hasTable('receivings')) {
            Schema::table('receivings', function (Blueprint $table) {
                if (!Schema::hasColumn('receivings', 'POID')) { $table->string('POID')->nullable()->after('id'); }
                if (!Schema::hasColumn('receivings', 'ReceivedDate')) { $table->dateTime('ReceivedDate')->nullable(); }
                if (!Schema::hasColumn('receivings', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
            });
        }

        // 9. Receiving Items
        if (Schema::hasTable('receiving_items')) {
            Schema::table('receiving_items', function (Blueprint $table) {
                if (!Schema::hasColumn('receiving_items', 'ReceivingID')) { $table->unsignedBigInteger('ReceivingID')->nullable()->after('id'); }
                if (!Schema::hasColumn('receiving_items', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable(); }
                if (!Schema::hasColumn('receiving_items', 'QuantityOnHand')) { $table->float('QuantityOnHand')->default(0); }
                if (!Schema::hasColumn('receiving_items', 'ExpiryDate')) { $table->date('ExpiryDate')->nullable(); }
                if (!Schema::hasColumn('receiving_items', 'UnitCost')) { $table->float('UnitCost')->default(0); }
                if (!Schema::hasColumn('receiving_items', 'DateReceived')) { $table->date('DateReceived')->nullable(); }
                if (!Schema::hasColumn('receiving_items', 'WarehouseID')) { $table->unsignedBigInteger('WarehouseID')->nullable(); }
            });
        }
    }

    public function down(): void
    {
    }
};
