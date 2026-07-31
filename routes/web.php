<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\Device_detailsController;
use App\Http\Controllers\Barang_masukController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\IssueReportController;

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
    Route::get('/dashboard-client', [DashboardController::class, 'client'])->name('dashboard.client');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/laporan-kendala', [IssueReportController::class, 'index'])->name('issue_reports.index');
    Route::get('/laporan-kendala/create', [IssueReportController::class, 'create'])->name('issue_reports.create');
    Route::post('/laporan-kendala', [IssueReportController::class, 'store'])->name('issue_reports.store');
    Route::get('/laporan-kendala/{issue_report}', [IssueReportController::class, 'show'])->name('issue_reports.show');
    Route::patch('/laporan-kendala/{issue_report}/status', [IssueReportController::class, 'updateStatus'])->name('issue_reports.update_status');
    Route::post('/laporan-kendala/{issue_report}/messages', [IssueReportController::class, 'storeMessage'])->name('issue_reports.store_message');
    Route::get('/laporan-kendala/{issue_report}/messages', [IssueReportController::class, 'fetchMessages'])->name('issue_reports.fetch_messages');

    Route::middleware('admin')->group(function () {
        Route::get('/laporan/export/{format}', [LaporanController::class, 'export'])
            ->whereIn('format', ['excel', 'pdf'])
            ->name('laporan.export');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('locations/search-locations', [LocationsController::class, 'searchLocations'])->name('locations.search_locations');
        Route::resource('locations', LocationsController::class);
        Route::get('items/search-items', [ItemsController::class, 'searchItems'])->name('items.search_items');
        Route::get('items/{item}/history/export/{format}', [ItemsController::class, 'exportHistory'])
            ->whereIn('format', ['excel', 'pdf'])
            ->name('items.history.export');
        Route::get('items/{item}/detail', [ItemsController::class, 'detail'])->name('items.detail');
        Route::resource('items', ItemsController::class);
        // Route AJAX search SN untuk Select2
        Route::get('device_details/search-items', [Device_detailsController::class, 'searchItems'])->name('device_details.search_items');
        Route::get('device_details/search-device-details', [Device_detailsController::class, 'searchDeviceDetails'])->name('device_details.search_device_details');
        Route::resource('device_details', Device_detailsController::class);
        Route::get('/search-items', [DistributionController::class, 'search'])->name('distribution.search_items');
        Route::get('distribution/search_distribution', [DistributionController::class, 'searchDistribution'])->name('distribution.search_distribution');
        Route::get('/get-ruangan', [DistributionController::class, 'getRuangan'])->name('distribution.get_ruangan');
        Route::post('/distribution/{id}/return', [DistributionController::class, 'returnItem'])->name('distribution.return');
        Route::put('/distribution-item/{id}/return', [DistributionController::class, 'returnItem']);
        Route::get('/distribution/report-detail/export/{format}', [DistributionController::class, 'exportReportDetail'])
            ->whereIn('format', ['excel', 'pdf'])
            ->name('distribution.report_detail.export');
        Route::get('/distribution/report-detail', [DistributionController::class, 'reportDetail'])->name('distribution.report_detail');
        Route::resource('distribution', DistributionController::class);
        Route::get('barang_masuk/search-barang-masuk', [Barang_masukController::class, 'searchBarangMasuk'])->name('barang_masuk.search_barang_masuk');
        Route::get('barang_masuk/export/{format}', [Barang_masukController::class, 'export'])
            ->whereIn('format', ['excel', 'pdf'])
            ->name('barang_masuk.export');
        Route::get('barang_masuk/{barang_masuk}/koreksi-sn', [Barang_masukController::class, 'koreksiSn'])->name('barang_masuk.koreksi_sn');
        Route::post('barang_masuk/{barang_masuk}/koreksi-sn', [Barang_masukController::class, 'updateKoreksiSn'])->name('barang_masuk.update_koreksi_sn');
        Route::resource('barang_masuk', Barang_masukController::class);
        Route::get('users/search-users', [UserManagementController::class, 'searchUsers'])->name('users.search_users');
        Route::resource('users', UserManagementController::class)->except(['show']);
    });
});
