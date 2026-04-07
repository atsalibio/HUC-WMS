<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Requisitions
        if (Schema::hasTable('Requisition')) {
            Schema::table('Requisition', function (Blueprint $table) {
                if (!Schema::hasColumn('Requisition', 'HealthCenterID')) { $table->unsignedBigInteger('HealthCenterID')->nullable()->after('RequisitionID'); }
                if (!Schema::hasColumn('Requisition', 'HealthCenterName')) { $table->string('HealthCenterName')->nullable(); }
                if (!Schema::hasColumn('Requisition', 'HealthCenterAddress')) { $table->string('HealthCenterAddress')->nullable(); }
                if (!Schema::hasColumn('Requisition', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
                if (!Schema::hasColumn('Requisition', 'RequestDate')) { $table->dateTime('RequestDate')->nullable(); }
                if (!Schema::hasColumn('Requisition', 'StatusType')) { $table->string('StatusType')->default('Pending'); }
            });
        }

        // 2. Requisition Items
        if (Schema::hasTable('RequisitionItem')) {
            Schema::table('RequisitionItem', function (Blueprint $table) {
                if (!Schema::hasColumn('RequisitionItem', 'RequisitionID')) { $table->unsignedBigInteger('RequisitionID')->nullable()->after('RequisitionItemID'); }
                if (!Schema::hasColumn('RequisitionItem', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable(); }
                if (!Schema::hasColumn('RequisitionItem', 'QuantityRequested')) { $table->float('QuantityRequested')->default(0); }
                if (!Schema::hasColumn('RequisitionItem', 'QuantityReceived')) { $table->float('QuantityReceived')->default(0); }
                if (!Schema::hasColumn('RequisitionItem', 'QuantityIssued')) { $table->float('QuantityIssued')->default(0); }
            });
        }

        // 3. Batches
        if (Schema::hasTable('CentralInventoryBatch')) {
            Schema::table('CentralInventoryBatch', function (Blueprint $table) {
                if (!Schema::hasColumn('CentralInventoryBatch', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable()->after('BatchID'); }
                if (!Schema::hasColumn('CentralInventoryBatch', 'QuantityOnHand')) { $table->float('QuantityOnHand')->default(0); }
                if (!Schema::hasColumn('CentralInventoryBatch', 'QuantityReleased')) { $table->float('QuantityReleased')->default(0); }
                if (!Schema::hasColumn('CentralInventoryBatch', 'UnitCost')) { $table->float('UnitCost')->default(0); }
                if (!Schema::hasColumn('CentralInventoryBatch', 'ExpiryDate')) { $table->date('ExpiryDate')->nullable(); }
            });
        }

        // 4. HC Inventory Batches (Keep as is or rename to Pascal if needed)
        if (!Schema::hasTable('HCInventoryBatch')) {
            Schema::create('HCInventoryBatch', function (Blueprint $table) {
                $table->id('HCBatchID');
                $table->unsignedBigInteger('HealthCenterID');
                $table->unsignedBigInteger('ItemID');
                $table->unsignedBigInteger('BatchID');
                $table->string('LotNumber')->nullable();
                $table->date('ExpiryDate')->nullable();
                $table->float('QuantityReceived')->default(0);
                $table->float('QuantityOnHand')->default(0);
                $table->dateTime('DateReceivedAtHC')->nullable();
                $table->timestamps();
            });
        }

        // 5. Issuances
        if (Schema::hasTable('Issuance')) {
            Schema::table('Issuance', function (Blueprint $table) {
                if (!Schema::hasColumn('Issuance', 'RequisitionID')) { $table->unsignedBigInteger('RequisitionID')->nullable()->after('IssuanceID'); }
                if (!Schema::hasColumn('Issuance', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
                if (!Schema::hasColumn('Issuance', 'IssueDate')) { $table->dateTime('IssueDate')->nullable(); }
                if (!Schema::hasColumn('Issuance', 'StatusType')) { $table->string('StatusType')->default('Pending'); }
            });
        }

        // 6. Issuance Items
        if (Schema::hasTable('IssuanceItem')) {
            Schema::table('IssuanceItem', function (Blueprint $table) {
                if (!Schema::hasColumn('IssuanceItem', 'IssuanceID')) { $table->unsignedBigInteger('IssuanceID')->nullable()->after('IssuanceItemID'); }
                if (!Schema::hasColumn('IssuanceItem', 'BatchID')) { $table->unsignedBigInteger('BatchID')->nullable(); }
                if (!Schema::hasColumn('IssuanceItem', 'RequisitionItemID')) { $table->unsignedBigInteger('RequisitionItemID')->nullable(); }
                if (!Schema::hasColumn('IssuanceItem', 'QuantityIssued')) { $table->float('QuantityIssued')->default(0); }
            });
        }

        // 7. Procurement Orders
        if (Schema::hasTable('ProcurementOrder')) {
            Schema::table('ProcurementOrder', function (Blueprint $table) {
                if (!Schema::hasColumn('ProcurementOrder', 'StatusType')) { $table->string('StatusType')->default('Pending')->after('POID'); }
            });
        }

        // 8. Receivings
        if (Schema::hasTable('Receiving')) {
            Schema::table('Receiving', function (Blueprint $table) {
                if (!Schema::hasColumn('Receiving', 'POID')) { $table->string('POID')->nullable()->after('ReceivingID'); }
                if (!Schema::hasColumn('Receiving', 'ReceivedDate')) { $table->dateTime('ReceivedDate')->nullable(); }
                if (!Schema::hasColumn('Receiving', 'UserID')) { $table->unsignedBigInteger('UserID')->nullable(); }
            });
        }

        // 9. Receiving Items
        if (Schema::hasTable('ReceivingItem')) {
            Schema::table('ReceivingItem', function (Blueprint $table) {
                if (!Schema::hasColumn('ReceivingItem', 'ReceivingID')) { $table->unsignedBigInteger('ReceivingID')->nullable()->after('ReceivingItemID'); }
                if (!Schema::hasColumn('ReceivingItem', 'ItemID')) { $table->unsignedBigInteger('ItemID')->nullable(); }
                if (!Schema::hasColumn('ReceivingItem', 'QuantityReceived')) { $table->float('QuantityReceived')->default(0); }
                if (!Schema::hasColumn('ReceivingItem', 'ExpiryDate')) { $table->date('ExpiryDate')->nullable(); }
                if (!Schema::hasColumn('ReceivingItem', 'UnitCost')) { $table->float('UnitCost')->default(0); }
                if (!Schema::hasColumn('ReceivingItem', 'DateReceived')) { $table->date('DateReceived')->nullable(); }
                if (!Schema::hasColumn('ReceivingItem', 'WarehouseID')) { $table->unsignedBigInteger('WarehouseID')->nullable(); }
            });
        }
    }

    public function down(): void
    {
    }
};
