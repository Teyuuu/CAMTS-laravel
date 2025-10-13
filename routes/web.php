<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AlertsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Sales Routes
Route::prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('sales');
    Route::post('/', [SalesController::class, 'store'])->name('sales.store');
    Route::delete('/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
});

// Inventory Routes
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/add', [InventoryController::class, 'add'])->name('inventory.add');
    Route::post('/update', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
});

// Accounts Payable Routes
Route::prefix('accounts')->group(function () {
    Route::get('/', [AccountsController::class, 'index'])->name('accounts');
    Route::post('/add', [AccountsController::class, 'add'])->name('accounts.add');
    Route::post('/pay', [AccountsController::class, 'pay'])->name('accounts.pay');
    Route::get('/edit/{id}', [AccountsController::class, 'edit'])->name('accounts.edit');
    Route::put('/{id}', [AccountsController::class, 'update'])->name('accounts.update');
    Route::delete('/{id}', [AccountsController::class, 'destroy'])->name('accounts.destroy');
});

// Delivery Routes
Route::prefix('delivery')->group(function () {
    Route::get('/', [DeliveryController::class, 'index'])->name('delivery');
    Route::post('/add', [DeliveryController::class, 'add'])->name('delivery.add');
    Route::post('/update', [DeliveryController::class, 'update'])->name('delivery.update');
    Route::post('/cancel', [DeliveryController::class, 'cancel'])->name('delivery.cancel');
    Route::get('/{id}', [DeliveryController::class, 'show'])->name('delivery.show');
});

// Attendance Routes
Route::prefix('attendance')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/time-in', [AttendanceController::class, 'timeIn'])->name('attendance.time-in');
    Route::post('/time-out', [AttendanceController::class, 'timeOut'])->name('attendance.time-out');
});

// Alerts Route
Route::get('/alerts', [AlertsController::class, 'index'])->name('alerts');