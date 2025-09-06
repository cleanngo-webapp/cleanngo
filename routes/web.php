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
Route::view('/admin', 'admin.dashboard')->middleware(['auth','role:admin'])->name('preview.admin');
Route::view('/employee', 'employee.dashboard')->middleware(['auth','role:employee'])->name('preview.employee');
Route::view('/customer', 'customer.dashboard')->middleware(['auth','role:customer'])->name('preview.customer');
