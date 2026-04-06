<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\HCInventoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthCenterController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\PatientRequisitionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupplierController;

Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications')->middleware('auth');
Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('api.notifications.read')->middleware('auth');

Route::post('/switch-health-center', [HealthCenterController::class, 'switch'])
    ->name('api.switchHealthCenter')
    ->middleware('auth');

Route::get('/health-centers', [HealthCenterController::class, 'index'])
    ->name('api.healthCenters')
    ->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/', [AuthController::class, 'showLogin'])->name('home');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::post('/requisitions/local', [RequisitionController::class, 'storeLocal'])->name('requisitions.local.store');
    Route::patch('/requisitions/{id}/status', [RequisitionController::class, 'updateStatus'])->name('requisitions.updateStatus');
    Route::post('/issuances/process', [IssuanceController::class, 'process'])->name('issuances.process');
    Route::post('/receivings', [ReceivingController::class, 'receive'])->name('receivings.receive');
    
    // New Ported Routes
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
    
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    
    Route::post('/procurement/orders', [ProcurementController::class, 'store'])->name('procurement.orders.store');
    Route::patch('/procurement/orders/{id}/status', [ProcurementController::class, 'updateStatus'])->name('procurement.orders.updateStatus');
    
    Route::post('/patients', [PatientRequisitionController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/search', [PatientRequisitionController::class, 'searchPatients'])->name('patients.search');
    Route::post('/patient-requisitions', [PatientRequisitionController::class, 'storeRequisition'])->name('patient-requisitions.store');
    Route::patch('/patient-requisitions/{id}/status', [PatientRequisitionController::class, 'updateStatus'])->name('patient-requisitions.updateStatus');

    Route::get('/pages/adjustments', [AdjustmentController::class, 'index'])->name('pages.adjustments');
    Route::post('/adjustments/disposal', [AdjustmentController::class, 'storeDisposal']);
    Route::post('/adjustments/return', [AdjustmentController::class, 'storeReturn']);
    Route::post('/adjustments/correction', [AdjustmentController::class, 'storeCorrection']);

    Route::get('/pages/history', [MonitoringController::class, 'index'])->name('pages.history');
    Route::post('/history/data', [MonitoringController::class, 'getHistory']);

    Route::get('/pages/reports', [ReportController::class, 'index'])->name('pages.reports');
    Route::post('/reports/generate', [ReportController::class, 'generate']);

    Route::get('/pages/inventory', [InventoryController::class, 'index'])->name('pages.inventory');
    Route::post('/inventory/item', [InventoryController::class, 'storeItem']);
    Route::get('/pages/dpri_import', [InventoryController::class, 'dpriIndex'])->name('pages.dpri_import');
    Route::post('/inventory/bulk-import', [InventoryController::class, 'bulkImport']);

    Route::get('/pages/hc_inventory', [HCInventoryController::class, 'index'])->name('pages.hc_inventory');

    Route::post('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile']);
    Route::post('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword']);
    Route::get('/settings/logs', [\App\Http\Controllers\SettingsController::class, 'getLogs']);

    Route::get('/pages/{page}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:Administrator');

    Route::get('/pharmacist/dashboard', [DashboardController::class, 'index'])
        ->name('pharmacist.dashboard')
        ->middleware('role:Head Pharmacist');

    Route::get('/health/dashboard', [DashboardController::class, 'index'])
        ->name('health.dashboard')
        ->middleware('role:Health Center Staff');

    Route::get('/warehouse/dashboard', [DashboardController::class, 'index'])
        ->name('warehouse.dashboard')
        ->middleware('role:Warehouse Staff');

    Route::get('/accounting/dashboard', [DashboardController::class, 'index'])
        ->name('accounting.dashboard')
        ->middleware('role:Accounting Office User');

    Route::get('/cmo/dashboard', [DashboardController::class, 'index'])
        ->name('cmo.dashboard')
        ->middleware('role:CMO/GSO/COA User');
});