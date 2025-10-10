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
use App\Http\Controllers\Employee\EmployeeProfileController;
use App\Http\Controllers\Employee\EmployeeJobsController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Admin\AdminEmployeeController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\CustomerGalleryController;
use App\Http\Controllers\Customer\CustomerSettingsController;
use App\Http\Controllers\Customer\CustomerServiceCommentController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Employee\EmployeeSettingsController;
use App\Http\Controllers\Admin\AdminPayrollController;
use App\Http\Controllers\Employee\EmployeePayrollController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Employee\EmployeeNotificationController;
use App\Http\Controllers\ServicesController;

// Public landing page route
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Redirect /login to landing page if not authenticated
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

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

// Public pages (no authentication required)
Route::get('/services', [ServicesController::class, 'index'])->name('services');

// Auth routes
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email verification routes
Route::get('/email-verification', [App\Http\Controllers\EmailVerificationController::class, 'showVerificationForm'])->name('email.verification.form');
Route::post('/email-verification/send-otp', [App\Http\Controllers\EmailVerificationController::class, 'sendOTP'])->name('email.verification.send');
Route::post('/email-verification/verify', [App\Http\Controllers\EmailVerificationController::class, 'verifyOTP'])->name('email.verification.verify');
Route::post('/email-verification/resend', [App\Http\Controllers\EmailVerificationController::class, 'resendOTP'])->name('email.verification.resend');

// Role redirector
Route::get('/dashboard', [AuthController::class, 'redirectByRole'])->name('dashboard.redirect');

// Debug route to test inventory transactions
Route::get('/debug/inventory-transactions', function() {
    $count = \App\Models\InventoryTransaction::count();
    $transactions = \App\Models\InventoryTransaction::with(['inventoryItem', 'employee.user', 'booking'])
        ->limit(5)
        ->get();
    
    return response()->json([
        'total_count' => $count,
        'recent_transactions' => $transactions->map(function($t) {
            return [
                'id' => $t->id,
                'type' => $t->transaction_type,
                'quantity' => $t->quantity,
                'item_name' => $t->inventoryItem ? $t->inventoryItem->name : 'NULL',
                'employee_name' => ($t->employee && $t->employee->user) 
                    ? $t->employee->user->first_name . ' ' . $t->employee->user->last_name 
                    : 'NULL',
                'booking_code' => $t->booking ? $t->booking->code : 'NULL'
            ];
        })
    ]);
});

// Simple preview routes for role dashboards (no auth/guards yet)
// Employee routes
Route::middleware(['auth:employee','role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jobs', [EmployeeJobsController::class, 'index'])->name('jobs');
    Route::get('/completed-jobs', [EmployeeJobsController::class, 'completedJobs'])->name('completed-jobs');
    Route::post('/jobs/{bookingId}/start', [EmployeeJobsController::class, 'start'])->name('jobs.start');
    Route::post('/jobs/{bookingId}/complete', [EmployeeJobsController::class, 'complete'])->name('jobs.complete');
    Route::post('/payment-proof/{bookingId}/upload', [App\Http\Controllers\Admin\PaymentProofController::class, 'upload'])->name('payment-proof.upload');
    Route::get('/payment-proof/{proofId}/details', [App\Http\Controllers\Admin\PaymentProofController::class, 'getDetails'])->name('payment-proof.details');
    Route::get('/bookings/{bookingId}/summary', [EmployeeJobsController::class, 'getSummary'])->name('bookings.summary');
    Route::get('/bookings/{bookingId}/location', [EmployeeJobsController::class, 'getLocation'])->name('bookings.location');
    Route::get('/bookings/{bookingId}/photos', [EmployeeJobsController::class, 'getPhotos'])->name('bookings.photos');
    Route::get('/inventory/available', [EmployeeJobsController::class, 'getAvailableInventory'])->name('inventory.available');
    Route::post('/jobs/{bookingId}/equipment/borrow', [EmployeeJobsController::class, 'borrowEquipment'])->name('jobs.equipment.borrow');
    Route::get('/jobs/{bookingId}/borrowed-items', [EmployeeJobsController::class, 'getBorrowedItems'])->name('jobs.borrowed-items');
    Route::get('/jobs/table-data', [EmployeeJobsController::class, 'getTableData'])->name('jobs.table-data');
    // Payment status polling route removed - no longer needed
    Route::get('/payroll', [EmployeePayrollController::class, 'index'])->name('payroll');
    Route::get('/notifications', [EmployeeNotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/api', [EmployeeNotificationController::class, 'getNotifications'])->name('notifications.api');
    Route::get('/notifications/dropdown', [EmployeeNotificationController::class, 'getDropdownNotifications'])->name('notifications.dropdown');
    Route::post('/notifications/mark-read', [EmployeeNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [EmployeeNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [EmployeeNotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
    // Settings routes for employee
    Route::get('/settings', [EmployeeSettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [EmployeeSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/payment', [EmployeeSettingsController::class, 'updatePaymentSettings'])->name('settings.payment.update');
    // Calendar events feed for employee (own assignments only)
    Route::get('/calendar/events', [CalendarController::class, 'employeeEvents'])->name('calendar.events');
});
Route::middleware(['auth:customer','role:customer'])->group(function () {
    Route::get('/customer', [CustomerHomeController::class, 'home'])->name('customer.dashboard');
    Route::get('/customer/home', [CustomerHomeController::class, 'home'])->name('preview.customer');
    Route::get('/customer/profile', [CustomerDashboardController::class, 'show'])->name('customer.profile');
    Route::post('/customer/bookings/search', [CustomerDashboardController::class, 'searchBookings'])->name('customer.bookings.search');
    Route::view('/customer/all-services', 'customer.allservices')->name('customer.allservices');
    Route::get('/customer/gallery', [CustomerGalleryController::class, 'index'])->name('customer.gallery');
    Route::get('/customer/gallery/{serviceType}', [CustomerGalleryController::class, 'showService'])->name('customer.gallery.service');
    Route::view('/customer/services', 'customer.services')->name('customer.services');
    Route::get('/customer/notifications', [CustomerNotificationController::class, 'index'])->name('customer.notifications');
    Route::get('/customer/notifications/api', [CustomerNotificationController::class, 'getNotifications'])->name('customer.notifications.api');
    Route::get('/customer/notifications/dropdown', [CustomerNotificationController::class, 'getDropdownNotifications'])->name('customer.notifications.dropdown');
    Route::post('/customer/notifications/mark-read', [CustomerNotificationController::class, 'markAsRead'])->name('customer.notifications.mark-read');
    Route::post('/customer/notifications/mark-all-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('customer.notifications.mark-all-read');
    Route::get('/customer/notifications/unread-count', [CustomerNotificationController::class, 'getUnreadCount'])->name('customer.notifications.unread-count');
    Route::get('/customer/settings', [CustomerSettingsController::class, 'index'])->name('customer.settings');
    Route::put('/customer/settings/password', [CustomerSettingsController::class, 'updatePassword'])->name('customer.settings.password.update');
    Route::put('/customer/settings/profile', [CustomerSettingsController::class, 'updateProfile'])->name('customer.settings.profile.update');
    Route::post('/customer/settings/avatar', [CustomerSettingsController::class, 'updateAvatar'])->name('customer.settings.avatar.update');
    Route::delete('/customer/settings/avatar', [CustomerSettingsController::class, 'removeAvatar'])->name('customer.settings.avatar.remove');
    Route::post('/customer/bookings', [CustomerBookingController::class, 'create'])->name('customer.bookings.create');
    Route::post('/customer/bookings/{bookingId}/cancel', [CustomerBookingController::class, 'cancel'])->name('customer.bookings.cancel');
    Route::post('/customer/addresses', [CustomerAddressController::class, 'store'])->name('customer.address.store');
    Route::delete('/customer/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('customer.address.destroy');
    Route::post('/customer/addresses/{address}/primary', [CustomerAddressController::class, 'setPrimary'])->name('customer.address.primary');
    
    // Customer Payment Proof routes
    Route::post('/customer/payment-proof/{bookingId}/upload', [App\Http\Controllers\Customer\CustomerPaymentProofController::class, 'upload'])->name('customer.payment-proof.upload');
    Route::get('/customer/payment-proof/{bookingId}/details', [App\Http\Controllers\Customer\CustomerPaymentProofController::class, 'getDetails'])->name('customer.payment-proof.details');
    
    // Service Comments routes
    Route::get('/service-comments/{serviceType}', [CustomerServiceCommentController::class, 'getComments'])->name('service.comments.get');
    Route::post('/service-comments', [CustomerServiceCommentController::class, 'store'])->name('service.comments.store');
    Route::put('/service-comments/{id}', [CustomerServiceCommentController::class, 'update'])->name('service.comments.update');
    Route::delete('/service-comments/{id}', [CustomerServiceCommentController::class, 'destroy'])->name('service.comments.destroy');
    
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
    Route::get('/completed-bookings', [AdminBookingController::class, 'completed'])->name('completed-bookings');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{bookingId}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/bookings/{bookingId}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{bookingId}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::match(['post','get'], '/bookings/{bookingId}/assign', [AdminBookingController::class, 'assignEmployee'])->name('bookings.assign');
    Route::get('/bookings/{bookingId}/employee-availability', [AdminBookingController::class, 'getEmployeeAvailability'])->name('bookings.employee-availability');
    Route::get('/bookings/employee-availability', [AdminBookingController::class, 'getEmployeeAvailabilityForManualBooking'])->name('bookings.employee-availability-manual');
    Route::post('/bookings/{bookingId}/assign-employees', [AdminBookingController::class, 'assignEmployees'])->name('bookings.assign-employees');
    Route::get('/bookings/{bookingId}/summary', [AdminBookingController::class, 'getSummary'])->name('bookings.summary');
    Route::get('/bookings/{bookingId}/location', [AdminBookingController::class, 'getLocation'])->name('bookings.location');
    Route::get('/bookings/{bookingId}/photos', [AdminBookingController::class, 'getPhotos'])->name('bookings.photos');
    Route::get('/payment-proof/{proofId}/details', [App\Http\Controllers\Admin\PaymentProofController::class, 'getDetails'])->name('payment-proof.details');
    Route::post('/payment-proof/{proofId}/approve', [App\Http\Controllers\Admin\PaymentProofController::class, 'approve'])->name('payment-proof.approve');
    Route::post('/payment-proof/{proofId}/decline', [App\Http\Controllers\Admin\PaymentProofController::class, 'decline'])->name('payment-proof.decline');
    Route::get('/employees', [AdminEmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [AdminEmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employee/{userId}', [AdminEmployeeController::class, 'show'])->name('employee.show');
    Route::put('/employee/{userId}', [AdminEmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employees/{userId}', [AdminEmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employee/{employeeId}/increment-jobs', [AdminEmployeeController::class, 'incrementJobsCompleted'])->name('employee.increment-jobs');
    Route::post('/employees/update-job-counts', [AdminEmployeeController::class, 'updateAllJobCounts'])->name('employees.update-job-counts');
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [AdminInventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory-stats', [AdminInventoryController::class, 'getStats'])->name('inventory.stats');
    Route::get('/inventory/transactions', [AdminInventoryController::class, 'getTransactions'])->name('inventory.transactions');
    Route::get('/inventory/{id}', [AdminInventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventory/{id}', [AdminInventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}', [AdminInventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers');
    Route::delete('/customers/{userId}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('gallery');
    Route::get('/gallery/{serviceType}', [AdminGalleryController::class, 'showService'])->name('gallery.service');
    Route::post('/gallery', [AdminGalleryController::class, 'store'])->name('gallery.store');
    Route::put('/gallery/{id}', [AdminGalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{id}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/service-comments/{serviceType}', [AdminGalleryController::class, 'getServiceComments'])->name('service.comments.admin');
    Route::delete('/service-comments/{id}', [AdminGalleryController::class, 'deleteServiceComment'])->name('service.comments.delete');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/payment', [AdminSettingsController::class, 'updatePaymentSettings'])->name('settings.payment.update');
    Route::get('/payroll', [AdminPayrollController::class, 'index'])->name('payroll');
    Route::post('/payroll/upload-payment', [AdminPayrollController::class, 'uploadPayment'])->name('payroll.upload-payment');
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/api', [AdminNotificationController::class, 'getNotifications'])->name('notifications.api');
    Route::get('/notifications/dropdown', [AdminNotificationController::class, 'getDropdownNotifications'])->name('notifications.dropdown');
    Route::post('/notifications/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [AdminNotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    // Calendar events feed for admin
    Route::get('/calendar/events', [CalendarController::class, 'adminEvents'])->name('calendar.events');
});
