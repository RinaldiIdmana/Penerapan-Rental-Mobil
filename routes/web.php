<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleReturnController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Reservasi & Manajemen Armada Rental Kendaraan
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->hasAdminPrivileges()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.catalog');
    }
    return redirect()->route('customer.catalog');
});

// Public Catalog & Availability
Route::get('/catalog', [VehicleController::class, 'customerCatalog'])->name('customer.catalog');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated General Routes (Customer & Admin)
Route::middleware('auth')->group(function () {
    // Booking Workflow
    Route::get('/reservations/book', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/my', [ReservationController::class, 'myReservations'])->name('reservations.my');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/api/reservations/cost-preview', [ReservationController::class, 'calculateCost'])->name('reservations.cost-preview');

    // Invoice View & PDF
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    Route::get('/invoices/{invoice}/stream', [InvoiceController::class, 'streamPdf'])->name('invoices.stream');
});

// Admin & Staff Protected Routes
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Modul 1: Availability Calendar & Double-Booking Check
    Route::get('/calendar', [AvailabilityCalendarController::class, 'index'])->name('calendar.index');
    Route::get('/api/calendar/events', [AvailabilityCalendarController::class, 'events'])->name('calendar.events');
    Route::post('/api/calendar/check', [AvailabilityCalendarController::class, 'check'])->name('calendar.check');

    // Fleet Management (Vehicles CRUD)
    Route::resource('vehicles', VehicleController::class)->except(['customerCatalog']);

    // Reservations Management
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');

    // Modul 3: Vehicle Returns & Late Fines
    Route::get('/returns', [VehicleReturnController::class, 'index'])->name('returns.index');
    Route::get('/reservations/{reservation}/return', [VehicleReturnController::class, 'create'])->name('returns.create');
    Route::post('/reservations/{reservation}/return', [VehicleReturnController::class, 'store'])->name('returns.store');
    Route::post('/api/reservations/{reservation}/return-preview', [VehicleReturnController::class, 'preview'])->name('returns.preview');

    // Modul 2: Invoices Management
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');

    // Modul 4: Maintenance Management & Alerts
    Route::resource('maintenances', MaintenanceController::class);
    Route::post('/maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
});
