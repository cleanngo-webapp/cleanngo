<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    /**
     * Display the employee dashboard with job assignments and progress
     * 
     * This method shows the employee their daily job assignments,
     * completed work, and pending tasks
     */
    public function index()
    {
        $employeeId = Auth::user()?->employee?->id;
        
        if (!$employeeId) {
            // If no employee record, redirect or show empty dashboard
            return view('employee.dashboard', [
                'jobsAssignedToday' => 0,
                'jobsCompletedToday' => 0,
                'pendingJobs' => 0,
                'todayJobs' => collect()
            ]);
        }
        
        $today = Carbon::today();
        
        // Get jobs assigned to this employee today or currently in progress
        // This includes jobs scheduled for today OR currently in progress
        $jobsAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where(function($query) use ($today) {
                $query->whereDate('bookings.scheduled_start', $today)
                      ->orWhere('bookings.status', 'in_progress');
            })
            ->count();
        
        // Get jobs completed by this employee today
        $jobsCompletedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where('bookings.status', 'completed')
            ->whereDate('bookings.completed_at', $today)
            ->count();
        
        // Get pending jobs assigned to this employee
        $pendingJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereIn('bookings.status', ['pending', 'confirmed'])
            ->count();
        
        // Get today's job details for the employee (scheduled today OR in progress)
        $todayJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('addresses', 'bookings.address_id', '=', 'addresses.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select(
                'bookings.id',
                'bookings.code',
                'bookings.status',
                'bookings.scheduled_start',
                'bookings.scheduled_end',
                'bookings.notes',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'addresses.line1 as street_address',
                'addresses.city',
                'addresses.latitude',
                'addresses.longitude',
                'services.name as service_name',
                'services.duration_minutes'
            )
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where(function($query) use ($today) {
                $query->whereDate('bookings.scheduled_start', $today)
                      ->orWhere('bookings.status', 'in_progress');
            })
            ->orderBy('bookings.scheduled_start')
            ->get();
        
        // Build locations data for map functionality
        $locationsData = $todayJobs->mapWithKeys(function ($job) {
            $parts = array_filter([
                $job->street_address ?? null, 
                $job->city ?? null
            ]);
            return [
                $job->id => [
                    'address' => implode(', ', $parts),
                    'lat' => $job->latitude,
                    'lng' => $job->longitude,
                    'phone' => $job->phone,
                ]
            ];
        })->all();
        
        // Build service summaries for better service display
        $serviceSummaries = [];
        $bookingIds = $todayJobs->pluck('id')->all();
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
                        $category = 'Sofa Cleaning';
                    } elseif (strpos($itemType, 'mattress') === 0) {
                        $category = 'Mattress Cleaning';
                    } elseif (strpos($itemType, 'car') === 0) {
                        $category = 'Car Cleaning';
                    } elseif (strpos($itemType, 'carpet') === 0) {
                        $category = 'Carpet Deep Cleaning';
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
        
        return view('employee.dashboard', compact(
            'jobsAssignedToday',
            'jobsCompletedToday', 
            'pendingJobs',
            'todayJobs',
            'locationsData',
            'serviceSummaries'
        ));
    }
}
