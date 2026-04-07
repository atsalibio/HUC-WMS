<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. HCPatient Table
        if (!Schema::hasTable('hc_patients')) {
            Schema::create('hc_patients', function (Blueprint $table) {
                $table->id('PatientID');
                $table->unsignedBigInteger('HealthCenterID')->nullable();
                $table->string('FName', 100);
                $table->string('MName', 100)->nullable();
                $table->string('LName', 100);
                $table->integer('Age')->nullable();
                $table->string('Gender', 50)->nullable();
                $table->text('Address')->nullable();
                $table->string('ContactNumber', 20)->nullable();
                $table->timestamps();
                
                // Index for faster searching
                $table->index(['LName', 'FName']);
            });
        }

        // 2. HCPatientRequisition Table
        if (!Schema::hasTable('hc_patient_requisitions')) {
            Schema::create('hc_patient_requisitions', function (Blueprint $table) {
                $table->id('PatientReqID');
                $table->unsignedBigInteger('PatientID');
                $table->unsignedBigInteger('UserID');
                $table->unsignedBigInteger('HealthCenterID');
                $table->string('RequisitionNumber', 100)->unique();
                $table->dateTime('RequestDate');
                $table->string('StatusType', 50)->default('Pending');
                $table->text('Diagnosis')->nullable();
                $table->text('Notes')->nullable();
                $table->string('ContactInfo', 255)->nullable();
                $table->string('IDProof', 255)->nullable();
                $table->timestamps();

                $table->index('StatusType');
            });
        }

        // 3. HCPatientRequisitionItem Table
        if (!Schema::hasTable('hc_patient_requisition_items')) {
            Schema::create('hc_patient_requisition_items', function (Blueprint $table) {
                $table->id('PRItemID');
                $table->unsignedBigInteger('PatientReqID');
                $table->unsignedBigInteger('ItemID');
                $table->integer('QuantityRequested');
                $table->timestamps();
                
                $table->index('PatientReqID');
                $table->index('ItemID');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hc_patient_requisition_items');
        Schema::dropIfExists('hc_patient_requisitions');
        Schema::dropIfExists('hc_patients');
    }
};
