<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthCenterController;

Route::post('/switch-health-center', [HealthCenterController::class, 'switch'])
    ->name('api.switchHealthCenter')
    ->middleware('auth');

Route::get('/health-centers', [HealthCenterController::class, 'index'])
    ->name('api.healthCenters')
    ->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
        ->name('admin.dashboard')
        ->middleware('role:Administrator');

    Route::get('/pharmacist/dashboard', fn() => view('pharmacist.dashboard'))
        ->name('pharmacist.dashboard')
        ->middleware('role:Head Pharmacist');

    Route::get('/health/dashboard', fn() => view('health.dashboard'))
        ->name('health.dashboard')
        ->middleware('role:Health Center Staff');

    Route::get('/warehouse/dashboard', fn() => view('warehouse.dashboard'))
        ->name('warehouse.dashboard')
        ->middleware('role:Warehouse Staff');
});