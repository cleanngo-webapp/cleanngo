<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeePayrollController extends Controller
{
    /**
     * Display the employee payroll page with their own payroll records
     * 
     * This method shows completed and paid bookings for the authenticated employee only.
     * It includes:
     * - Booking completion date
     * - Booking code
     * - Service summary (categorized from booking items)
     * - Customer name
     * - Payment amount
     * - Payment method
     * - Payment status
     * - Receipt viewing functionality
     * - Monthly earnings summary
     * - Monthly jobs completed count
     */
    public function index(Request $request)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return redirect()->route('employee.dashboard')->withErrors(['error' => 'Employee profile not found.']);
        }

        // Get search and sort parameters from request
        $search = $request->get('search');
        $sort = $request->get('sort', 'completed_at');
        $sortOrder = $request->get('sortOrder', 'desc');

        // Build the base query for payroll records (employee's own records only)
        $query = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'b.id', '=', 'bsa.booking_id')
            ->join('employees as e', 'bsa.employee_id', '=', 'e.id')
            ->join('users as eu', 'e.user_id', '=', 'eu.id')
            ->join('customers as c', 'b.customer_id', '=', 'c.id')
            ->join('users as cu', 'c.user_id', '=', 'cu.id')
            ->join('services as s', 'b.service_id', '=', 's.id')
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('b.id', '=', 'pp.booking_id')
                     ->where('pp.status', '=', 'approved');
            })
            ->where('b.status', 'completed')
            ->where('b.payment_status', 'paid')
            ->where('bsa.employee_id', $employeeId)
            ->select([
                'b.id as booking_id',
                'b.code as booking_code',
                'b.completed_at',
                'b.total_due_cents',
                'b.payment_method',
                'b.payment_status',
                DB::raw("CONCAT(eu.first_name, ' ', eu.last_name) as employee_name"),
                DB::raw("CONCAT(cu.first_name, ' ', cu.last_name) as customer_name"),
                's.name as service_name',
                'pp.amount as payment_amount'
            ]);
        
        // Apply search functionality - search across booking code and customer name
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere('cu.first_name', 'like', "%{$search}%")
                  ->orWhere('cu.last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(cu.first_name, ' ', cu.last_name) LIKE ?", ["%{$search}%"]);
            });
        }
        
        // Apply sorting with proper order
        $validSortFields = ['completed_at', 'total_due_cents'];
        $validSortOrders = ['asc', 'desc'];
        
        if (in_array($sort, $validSortFields) && in_array($sortOrder, $validSortOrders)) {
            $query->orderBy($sort, $sortOrder);
        } else {
            // Default sorting by completion date descending
            $query->orderByDesc('b.completed_at');
        }
        
        // Execute the query
        $payrollRecords = $query->get();

        // Build service summaries for better service display
        $serviceSummaries = [];
        $bookingIds = $payrollRecords->pluck('booking_id')->all();
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
                $total = 0.0;
                $serviceCategories = [];
                foreach ($lines as $ln) { 
                    $total += (float)($ln['line_total'] ?? 0);
                    // Map item types to service categories
                    $itemType = $ln['item_type'];
                    $category = '';
                    
                    if (strpos($itemType, 'sofa') === 0) {
                        $category = 'Sofa Mattress Deep Cleaning';
                    } elseif (strpos($itemType, 'mattress') === 0) {
                        $category = 'Mattress Deep Cleaning';
                    } elseif (strpos($itemType, 'carpet') === 0) {
                        $category = 'Carpet Deep Cleaning';
                    } elseif (strpos($itemType, 'car') === 0) {
                        $category = 'Home Service Car Interior Detailing';
                    } elseif (strpos($itemType, 'post_construction') === 0) {
                        $category = 'Post Construction Cleaning';
                    } elseif (strpos($itemType, 'disinfect') === 0) {
                        $category = 'Home/Office Disinfection';
                    } elseif (strpos($itemType, 'glass') === 0) {
                        $category = 'Glass Cleaning';
                    } elseif (strpos($itemType, 'house') === 0) {
                        $category = 'House Cleaning';
                    } elseif (strpos($itemType, 'curtain') === 0) {
                        $category = 'Curtain Cleaning';
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

        // Build receipt data for the receipt modal
        $receiptData = [];
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
                $total = 0.0;
                foreach ($lines as $ln) { 
                    $total += (float)($ln['line_total'] ?? 0);
                }
                $receiptData[$bid] = [ 'lines' => $lines, 'total' => $total ];
            }
        }

        // Calculate earnings summary for this month
        // Employee receives fixed 600 per completed job
        $monthlyJobsCompleted = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();
        
        // Employee earnings = 600 per job
        $monthlyEarnings = 600 * $monthlyJobsCompleted;

        return view('employee.payroll', [
            'payrollRecords' => $payrollRecords,
            'serviceSummaries' => $serviceSummaries,
            'receiptData' => $receiptData,
            'monthlyEarnings' => $monthlyEarnings,
            'monthlyJobsCompleted' => $monthlyJobsCompleted,
            'search' => $search,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
        ]);
    }
}
