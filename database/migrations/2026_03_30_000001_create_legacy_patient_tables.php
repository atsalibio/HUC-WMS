<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. HCPatient Table
        if (!Schema::hasTable('HCPatient')) {
            Schema::create('HCPatient', function (Blueprint $table) {
                $table->id('PatientID');
                $table->integer('HealthCenterID')->nullable();
                $table->string('FName', 100);
                $table->string('MName', 100)->nullable();
                $table->string('LName', 100);
                $table->integer('Age')->nullable();
                $table->enum('Gender', ['Male', 'Female', 'Other'])->nullable();
                $table->text('Address')->nullable();
                $table->string('ContactNumber', 20)->nullable();
                $table->timestamp('CreatedAt')->useCurrent();
            });
        }

        // 2. HCPatientRequisition Table
        if (!Schema::hasTable('HCPatientRequisition')) {
            Schema::create('HCPatientRequisition', function (Blueprint $table) {
                $table->id('PatientReqID');
                $table->integer('PatientID');
                $table->integer('UserID');
                $table->integer('HealthCenterID');
                $table->string('RequisitionNumber', 100)->unique()->nullable();
                $table->dateTime('RequestDate');
                $table->string('StatusType', 50)->default('Pending');
                $table->text('Diagnosis')->nullable();
                $table->text('Notes')->nullable();
                $table->string('ContactInfo', 255)->nullable();
                $table->string('IDProof', 255)->nullable();
                $table->timestamp('CreatedAt')->useCurrent();
            });
        }

        // 3. HCPatientRequisitionItem Table
        if (!Schema::hasTable('HCPatientRequisitionItem')) {
            Schema::create('HCPatientRequisitionItem', function (Blueprint $table) {
                $table->id('PRItemID');
                $table->integer('PatientReqID');
                $table->integer('ItemID');
                $table->integer('QuantityRequested');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('HCPatientRequisitionItem');
        Schema::dropIfExists('HCPatientRequisition');
        Schema::dropIfExists('HCPatient');
    }
};
