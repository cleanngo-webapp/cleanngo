<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\AdminEmployeeController;

Route::redirect('/', '/login');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role redirector
Route::get('/dashboard', [AuthController::class, 'redirectByRole'])->middleware('auth')->name('dashboard.redirect');

// Simple preview routes for role dashboards (no auth/guards yet)
// Employee routes
Route::middleware(['auth','role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::view('/', 'employee.dashboard')->name('dashboard');
    Route::view('/jobs', 'employee.jobs')->name('jobs');
    Route::view('/payroll', 'employee.payroll')->name('payroll');
    Route::view('/notifications', 'employee.notifications')->name('notifications');
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
    // Calendar events feed for employee (own assignments only)
    Route::get('/calendar/events', [CalendarController::class, 'employeeEvents'])->name('calendar.events');
});
Route::view('/customer', 'customer.dashboard')->middleware(['auth','role:customer'])->name('preview.customer');

// Admin routes with sidebar layout pages
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/bookings', 'admin.bookings')->name('bookings');
    Route::get('/employees', [AdminEmployeeController::class, 'index'])->name('employees');
    Route::get('/employee/{userId}', [AdminEmployeeController::class, 'show'])->name('employee.show');
    Route::put('/employee/{userId}', [AdminEmployeeController::class, 'update'])->name('employee.update');
    Route::post('/employee/{employeeId}/increment-jobs', [AdminEmployeeController::class, 'incrementJobsCompleted'])->name('employee.increment-jobs');
    Route::post('/employees/update-job-counts', [AdminEmployeeController::class, 'updateAllJobCounts'])->name('employees.update-job-counts');
    Route::view('/inventory', 'admin.inventory')->name('inventory');
    Route::view('/customers', 'admin.customers')->name('customers');
    Route::view('/gallery', 'admin.gallery')->name('gallery');
    Route::view('/settings', 'admin.settings')->name('settings');
    // Calendar events feed for admin
    Route::get('/calendar/events', [CalendarController::class, 'adminEvents'])->name('calendar.events');
});
