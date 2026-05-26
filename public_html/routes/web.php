<?php

use Illuminate\Support\Facades\Route;
// Controller Anda sudah benar semua di sini
use App\Http\Controllers\Guest\{PublicBookingController, TrackingController, PublicMonitorController};
use App\Http\Controllers\Customer\{CustomerDashboardController, CustomerBookingController, VehicleController};
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PrintController;
 

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicBookingController::class, 'landing'])->name('landing');
Route::get('/book', [PublicBookingController::class, 'create'])->name('book.create');
Route::post('/book', [PublicBookingController::class, 'store'])->name('book.store');

// Public Monitor (Ini sudah benar)
// Rute ini untuk menampilkan HALAMAN (monitor.blade.php)
Route::get('/monitor', [PublicMonitorController::class, 'index'])->name('public.monitor');
// Rute ini untuk mengambil DATA JSON (yang dipanggil oleh Alpine.js)
Route::get('/monitor/data', [PublicMonitorController::class, 'data'])->name('public.monitor.data');


/*
|--------------------------------------------------------------------------
| TRACKING ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('track')->name('track.')->group(function () {
    Route::get('/', [TrackingController::class, 'form'])->name('form');
    Route::get('/{code}', [TrackingController::class, 'show'])
        ->name('show')
        ->where('code', '(BK|WO)-\d{8}-\d{4}'); // Anda mungkin perlu menyesuaikan regex ini
    Route::get('/api/{code}', [TrackingController::class, 'json'])
        ->name('json')
        ->where('code', '(BK|WO)-\d{8}-\d{4}'); // Anda mungkin perlu menyesuaikan regex ini
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', function() {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'store']);
    
    Route::get('/register', function() {
        return view('auth.register');
    })->name('register');
    
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/download', [CustomerBookingController::class, 'downloadPDF'])
     ->name('bookings.download');

    // Vehicles (Sudah termasuk 'show')
    Route::resource('vehicles', VehicleController::class)->only([
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
    ]);
});

/*
|--------------------------------------------------------------------------
| ADMIN / OWNER / STAFF ROUTES (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:access-admin'])
    ->prefix('admin-api')->name('admin.')->group(function () {

    // Convert Booking -> WorkOrder
    Route::post('/bookings/{booking}/convert', [AdminBookingController::class, 'convertToWorkOrder'])
        ->name('bookings.convert');

    // Approve/Reject
    Route::post('/approvals/{approval}/approve', [AdminBookingController::class, 'approve'])
        ->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [AdminBookingController::class, 'reject'])
        ->name('approvals.reject');
});


/*
|--------------------------------------------------------------------------
| ADMIN PRINT ROUTES (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/reports/print-services', [ReportController::class, 'printServiceReport'])
        ->name('reports.print-services');

    // Rute 2: Kinerja Mekanik
    Route::get('/reports/print-mechanics', [ReportController::class, 'printMechanicReport'])
        ->name('reports.print-mechanics');
        
    // Rute 3: Ringkasan Analitik
    Route::get('/reports/print-analytics', [ReportController::class, 'printAnalyticsReport'])
        ->name('reports.print-analytics');     

    // Print individual booking - PERBAIKAN DI SINI (hapus /admin di depan)
    Route::get('/bookings/{id}/print', [PrintController::class, 'printBooking'])
        ->name('bookings.print');

    // Print individual work order - PERBAIKAN DI SINI (hapus /admin di depan)
    Route::get('/work-orders/{id}/print', [PrintController::class, 'printWorkOrder'])
        ->name('work-orders.print');

});