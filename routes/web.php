<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\DecorationCategoryController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer Management
    Route::resource('customers', CustomerController::class);

    // Stage Bookings
    Route::get('/bookings/{booking}/confirmation-slip', [BookingController::class, 'confirmationSlip'])->name('bookings.confirmation-slip');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::resource('bookings', BookingController::class);

    // Booking Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Payment Management & Ledger
    Route::get('/payments/ledger', [PaymentController::class, 'ledger'])->name('payments.ledger');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);

    // Expense Management
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Financial Reports & Profit/Loss
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Master Data Management
    Route::patch('/event-types/{eventType}/toggle', [EventTypeController::class, 'toggleStatus'])->name('event-types.toggle');
    Route::resource('event-types', EventTypeController::class)->except(['show', 'destroy']);

    Route::patch('/venues/{venue}/toggle', [VenueController::class, 'toggleStatus'])->name('venues.toggle');
    Route::resource('venues', VenueController::class)->except(['show', 'destroy']);

    Route::patch('/decoration-categories/{decorationCategory}/toggle', [DecorationCategoryController::class, 'toggleStatus'])->name('decoration-categories.toggle');
    Route::resource('decoration-categories', DecorationCategoryController::class)->except(['show', 'destroy']);

    // Admin-Only Routes
    Route::middleware('admin')->group(function () {
        Route::get('/settings/business', [BusinessSettingController::class, 'edit'])->name('settings.business');
        Route::put('/settings/business', [BusinessSettingController::class, 'update'])->name('settings.business.update');
        Route::resource('users', UserController::class);
        Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');
    });
});
