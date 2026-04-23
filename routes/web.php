<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\Device_detailsController;
use App\Http\Controllers\Barang_masukController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Routes untuk Login/Logout (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes untuk Dashboard (Auth only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('locations', LocationsController::class);
    Route::resource('items', ItemsController::class);
    // Route AJAX search SN untuk Select2
    Route::get('device_details/search-items', [Device_detailsController::class, 'searchItems'])->name('device_details.search_items');
    Route::get('device_details/search-device-details', [Device_detailsController::class, 'searchDeviceDetails'])->name('device_details.search_device_details');
    Route::resource('device_details', Device_detailsController::class);
    Route::get('/search-items', [DistributionController::class, 'search'])->name('distribution.search_items');
    Route::resource('distribution', DistributionController::class);
    Route::resource('barang_masuk', Barang_masukController::class);
});
