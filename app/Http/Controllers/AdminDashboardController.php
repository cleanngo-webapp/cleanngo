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
        
        // Get employees assigned today or currently working
        // This includes employees assigned to bookings scheduled for today OR currently in progress
        $employeesAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where(function($query) use ($today) {
                $query->whereDate('bookings.scheduled_start', $today)
                      ->orWhere('bookings.status', 'in_progress');
            })
            ->whereNotNull('booking_staff_assignments.employee_id')
            ->select('booking_staff_assignments.employee_id')
            ->distinct()
            ->get()
            ->count();
        
        // Get completed jobs today
        $completedJobsToday = DB::table('bookings')
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();
        
        // Get low stock items using the new inventory system
        // Status is automatically calculated based on quantity vs reorder level
        $lowStockItems = DB::table('inventory_items')
            ->where('status', 'Low Stock')
            ->where('is_active', true)
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
        
        // Build service summaries for the recent bookings
        // This creates a summary of service categories for each booking
        $serviceSummaries = [];
        $bookingIds = $recentBookings->pluck('id')->all();
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                $grouped[$r->booking_id][] = [
                    'item_type' => $r->item_type,
                    'quantity' => (int)($r->quantity ?? 0),
                    'area_sqm' => $r->area_sqm !== null ? (float)$r->area_sqm : null,
                    'unit_price' => $r->unit_price_cents !== null ? ((int)$r->unit_price_cents)/100 : null,
                    'line_total' => $r->line_total_cents !== null ? ((int)$r->line_total_cents)/100 : null,
                ];
            }
            foreach ($grouped as $bid => $lines) {
                $serviceCategories = [];
                foreach ($lines as $ln) { 
                    // Map item types to service categories for better display
                    $itemType = $ln['item_type'];
                    $category = '';
                    
                    if (strpos($itemType, 'sofa') === 0) {
                        $category = 'Sofa Deep Cleaning';
                    } elseif (strpos($itemType, 'mattress') === 0) {
                        $category = 'Mattress Deep Cleaning';
                    } elseif (strpos($itemType, 'carpet') === 0) {
                        $category = 'Carpet Deep Cleaning';
                    } elseif (strpos($itemType, 'car') === 0) {
                        $category = 'Home Service Car Interior Detailing';
                    } elseif (strpos($itemType, 'post_construction') === 0) {
                        $category = 'Post Construction Cleaning';
                    } elseif (strpos($itemType, 'disinfect') === 0) {
                        $category = 'Enhanced Disinfection';
                    } elseif (strpos($itemType, 'glass') === 0) {
                        $category = 'Glass Cleaning';
                    } else {
                        $category = ucwords(str_replace('_', ' ', $itemType));
                    }
                    
                    if (!in_array($category, $serviceCategories)) {
                        $serviceCategories[] = $category;
                    }
                }
                $serviceSummaries[$bid] = implode(', ', $serviceCategories);
            }
        }
        
        return view('admin.dashboard', compact(
            'totalBookings',
            'todayBookings', 
            'activeServices',
            'employeesAssignedToday',
            'completedJobsToday',
            'lowStockItems',
            'recentBookings',
            'serviceSummaries'
        ));
    }
}
