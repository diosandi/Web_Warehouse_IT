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
    Route::get('locations/search-locations', [LocationsController::class, 'searchLocations'])->name('locations.search_locations');
    Route::resource('locations', LocationsController::class);
    Route::get('items/search-items',[ItemsController::class,'searchItems'])->name('items.search_items');
    Route::resource('items', ItemsController::class);
    // Route AJAX search SN untuk Select2
    Route::get('device_details/search-items', [Device_detailsController::class, 'searchItems'])->name('device_details.search_items');
    Route::get('device_details/search-device-details', [Device_detailsController::class, 'searchDeviceDetails'])->name('device_details.search_device_details');
    Route::resource('device_details', Device_detailsController::class);
    Route::get('/search-items', [DistributionController::class, 'search'])->name('distribution.search_items');
    route::get('distribution/search_distribution',[DistributionController::class, 'searchDistribution'])->name('distribution.search_distribution');
    route::get('/get-ruangan',[DistributionController::class, 'getRuangan']);
    Route::resource('distribution', DistributionController::class);
    Route::get('barang_masuk/search-barang-masuk', [Barang_masukController::class, 'searchBarangMasuk'])->name('barang_masuk.search_barang_masuk');
    Route::resource('barang_masuk', Barang_masukController::class);
});
