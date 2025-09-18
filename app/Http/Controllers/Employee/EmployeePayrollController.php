<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return redirect()->route('employee.dashboard')->withErrors(['error' => 'Employee profile not found.']);
        }

        // Get completed and paid bookings for this employee only
        $payrollRecords = DB::table('bookings as b')
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
            ])
            ->orderByDesc('b.completed_at')
            ->get();

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
        $currentMonth = now()->format('Y-m');
        $monthlyEarnings = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->sum('total_due_cents') / 100;

        $monthlyJobsCompleted = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();

        return view('employee.payroll', [
            'payrollRecords' => $payrollRecords,
            'serviceSummaries' => $serviceSummaries,
            'receiptData' => $receiptData,
            'monthlyEarnings' => $monthlyEarnings,
            'monthlyJobsCompleted' => $monthlyJobsCompleted,
        ]);
    }
}
