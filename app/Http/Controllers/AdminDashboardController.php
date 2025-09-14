<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics
     * 
     * This method fetches and displays important business metrics
     * including booking statistics, employee assignments, and service status
     */
    public function index()
    {
        $today = Carbon::today();
        
        // Get total bookings count from all time
        $totalBookings = DB::table('bookings')->count();
        
        // Get today's bookings count
        $todayBookings = DB::table('bookings')
            ->whereDate('scheduled_start', $today)
            ->count();
        
        // Get active services (bookings in progress)
        $activeServices = DB::table('bookings')
            ->where('status', 'in_progress')
            ->count();
        
        // Get employees assigned today
        $employeesAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->whereDate('bookings.scheduled_start', $today)
            ->distinct('booking_staff_assignments.employee_id')
            ->count('booking_staff_assignments.employee_id');
        
        // Get completed jobs today
        $completedJobsToday = DB::table('bookings')
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();
        
        // Get low stock items using the inventory stock levels view
        // This view calculates current stock based on transactions
        $lowStockItems = DB::table('inventory_stock_levels')
            ->join('inventory_items', 'inventory_stock_levels.item_id', '=', 'inventory_items.id')
            ->whereRaw('inventory_stock_levels.qty_on_hand <= inventory_items.min_stock')
            ->where('inventory_items.is_active', true)
            ->count();
        
        // Get recent bookings for display
        $recentBookings = DB::table('bookings')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select(
                'bookings.id',
                'bookings.code',
                'bookings.status',
                'bookings.scheduled_start',
                'bookings.total_due_cents',
                'users.first_name',
                'users.last_name',
                'services.name as service_name'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalBookings',
            'todayBookings', 
            'activeServices',
            'employeesAssignedToday',
            'completedJobsToday',
            'lowStockItems',
            'recentBookings'
        ));
    }
}
