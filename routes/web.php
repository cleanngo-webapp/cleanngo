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
use App\Http\Controllers\EmployeeJobsController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\AdminEmployeeController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminGalleryController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerGalleryController;
use App\Http\Controllers\ServiceCommentController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\EmployeeSettingsController;
use App\Http\Controllers\AdminPayrollController;
use App\Http\Controllers\EmployeePayrollController;

Route::redirect('/', '/login');

// Sitemap route
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap')->header('Content-Type', 'application/xml');
})->name('sitemap');

// Legal pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role redirector
Route::get('/dashboard', [AuthController::class, 'redirectByRole'])->name('dashboard.redirect');

// Simple preview routes for role dashboards (no auth/guards yet)
// Employee routes
Route::middleware(['auth:employee','role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jobs', [EmployeeJobsController::class, 'index'])->name('jobs');
    Route::post('/jobs/{bookingId}/start', [EmployeeJobsController::class, 'start'])->name('jobs.start');
    Route::post('/jobs/{bookingId}/complete', [EmployeeJobsController::class, 'complete'])->name('jobs.complete');
    Route::post('/payment-proof/{bookingId}/upload', [App\Http\Controllers\PaymentProofController::class, 'upload'])->name('payment-proof.upload');
    Route::get('/payroll', [EmployeePayrollController::class, 'index'])->name('payroll');
    Route::view('/notifications', 'employee.notifications')->name('notifications');
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
    // Settings routes for employee
    Route::get('/settings', [EmployeeSettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [EmployeeSettingsController::class, 'updatePassword'])->name('settings.password.update');
    // Calendar events feed for employee (own assignments only)
    Route::get('/calendar/events', [CalendarController::class, 'employeeEvents'])->name('calendar.events');
});
Route::middleware(['auth:customer','role:customer'])->group(function () {
    Route::get('/customer', [CustomerHomeController::class, 'home'])->name('preview.customer');
    Route::get('/customer/profile', [CustomerDashboardController::class, 'show'])->name('customer.profile');
    Route::view('/customer/all-services', 'customer.allservices')->name('customer.allservices');
    Route::get('/customer/gallery', [CustomerGalleryController::class, 'index'])->name('customer.gallery');
    Route::get('/customer/gallery/{serviceType}', [CustomerGalleryController::class, 'showService'])->name('customer.gallery.service');
    Route::view('/customer/services', 'customer.services')->name('customer.services');
    Route::post('/customer/bookings', [CustomerBookingController::class, 'create'])->name('customer.bookings.create');
    Route::post('/customer/bookings/{bookingId}/cancel', [CustomerBookingController::class, 'cancel'])->name('customer.bookings.cancel');
    Route::post('/customer/addresses', [CustomerAddressController::class, 'store'])->name('customer.address.store');
    Route::delete('/customer/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('customer.address.destroy');
    Route::post('/customer/addresses/{address}/primary', [CustomerAddressController::class, 'setPrimary'])->name('customer.address.primary');
    
    // Service Comments routes
    Route::get('/service-comments/{serviceType}', [ServiceCommentController::class, 'getComments'])->name('service.comments.get');
    Route::post('/service-comments', [ServiceCommentController::class, 'store'])->name('service.comments.store');
    Route::put('/service-comments/{id}', [ServiceCommentController::class, 'update'])->name('service.comments.update');
    Route::delete('/service-comments/{id}', [ServiceCommentController::class, 'destroy'])->name('service.comments.destroy');
    
    // Debug route to check comments
    Route::get('/debug-comments', function() {
        $comments = App\Models\ServiceComment::with('customer')->get();
        return response()->json([
            'total_comments' => $comments->count(),
            'comments' => $comments->map(function($c) {
                return [
                    'id' => $c->id,
                    'service_type' => $c->service_type,
                    'is_approved' => $c->is_approved,
                    'customer_id' => $c->customer_id,
                    'customer_name' => $c->customer ? $c->customer->first_name : 'No customer',
                    'comment_preview' => substr($c->comment, 0, 50)
                ];
            })
        ]);
    });
});

// Admin routes with sidebar layout pages
Route::middleware(['auth:admin','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::view('/bookings', 'admin.bookings')->name('bookings');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{bookingId}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/bookings/{bookingId}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::match(['post','get'], '/bookings/{bookingId}/assign', [AdminBookingController::class, 'assignEmployee'])->name('bookings.assign');
    Route::get('/payment-proof/{proofId}/details', [App\Http\Controllers\PaymentProofController::class, 'getDetails'])->name('payment-proof.details');
    Route::post('/payment-proof/{proofId}/approve', [App\Http\Controllers\PaymentProofController::class, 'approve'])->name('payment-proof.approve');
    Route::post('/payment-proof/{proofId}/decline', [App\Http\Controllers\PaymentProofController::class, 'decline'])->name('payment-proof.decline');
    Route::get('/employees', [AdminEmployeeController::class, 'index'])->name('employees');
    Route::get('/employee/{userId}', [AdminEmployeeController::class, 'show'])->name('employee.show');
    Route::put('/employee/{userId}', [AdminEmployeeController::class, 'update'])->name('employee.update');
    Route::post('/employee/{employeeId}/increment-jobs', [AdminEmployeeController::class, 'incrementJobsCompleted'])->name('employee.increment-jobs');
    Route::post('/employees/update-job-counts', [AdminEmployeeController::class, 'updateAllJobCounts'])->name('employees.update-job-counts');
    Route::view('/inventory', 'admin.inventory')->name('inventory');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers');
    Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('gallery');
    Route::get('/gallery/{serviceType}', [AdminGalleryController::class, 'showService'])->name('gallery.service');
    Route::post('/gallery', [AdminGalleryController::class, 'store'])->name('gallery.store');
    Route::put('/gallery/{id}', [AdminGalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{id}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/payroll', [AdminPayrollController::class, 'index'])->name('payroll');
    // Calendar events feed for admin
    Route::get('/calendar/events', [CalendarController::class, 'adminEvents'])->name('calendar.events');
});
