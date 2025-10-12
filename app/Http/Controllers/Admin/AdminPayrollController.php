<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPayrollController extends Controller
{
    /**
     * Display the admin payroll page with all employee payroll records
     * 
     * This method shows completed and paid bookings for all employees.
     * It includes:
     * - Booking completion date
     * - Booking code
     * - Service summary (categorized from booking items)
     * - Customer name
     * - Employee name
     * - Payment amount
     * - Payment method
     * - Payment status
     * - Receipt viewing functionality
     */
    public function index(Request $request)
    {
        // Get search and sort parameters from request
        $search = $request->get('search');
        $sort = $request->get('sort', 'completed_at');
        $sortOrder = $request->get('sortOrder', 'desc');
        
        // Build the base query for payroll records - show all employees assigned to completed bookings
        $query = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'b.id', '=', 'bsa.booking_id')
            ->join('employees as e', 'bsa.employee_id', '=', 'e.id')
            ->join('users as eu', 'e.user_id', '=', 'eu.id')
            ->join('customers as c', 'b.customer_id', '=', 'c.id')
            ->join('users as cu', 'c.user_id', '=', 'cu.id')
            ->join('services as s', 'b.service_id', '=', 's.id')
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('b.id', '=', 'pp.booking_id')
                     ->on('bsa.employee_id', '=', 'pp.employee_id')
                     ->where('pp.status', '=', 'approved');
            })
            ->leftJoin('payment_settings as ps', function($join) {
                $join->on('eu.id', '=', 'ps.user_id')
                     ->where('ps.is_active', '=', true);
            })
            ->where('b.status', 'completed')
            ->where('b.payment_status', 'paid')
            ->select([
                'b.id as booking_id',
                'b.code as booking_code',
                'b.completed_at',
                'b.total_due_cents',
                'b.payment_method',
                'b.payment_status',
                'e.id as employee_id',
                'eu.id as employee_user_id',
                DB::raw("CONCAT(eu.first_name, ' ', eu.last_name) as employee_name"),
                DB::raw("CONCAT(cu.first_name, ' ', cu.last_name) as customer_name"),
                's.name as service_name',
                'pp.amount as payment_amount',
                'pp.payroll_code',
                'pp.payroll_status',
                'pp.payroll_amount',
                'pp.payroll_proof',
                'pp.payroll_method',
                'ps.gcash_name',
                'ps.gcash_number',
                'ps.qr_code_path'
            ]);
        
        // Apply search functionality - search across booking code, customer name, and employee name
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere('cu.first_name', 'like', "%{$search}%")
                  ->orWhere('cu.last_name', 'like', "%{$search}%")
                  ->orWhere('eu.first_name', 'like', "%{$search}%")
                  ->orWhere('eu.last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(cu.first_name, ' ', cu.last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(eu.first_name, ' ', eu.last_name) LIKE ?", ["%{$search}%"]);
            });
        }
        
        // Apply sorting with proper order
        $validSortFields = ['completed_at', 'employee_name', 'total_due_cents'];
        $validSortOrders = ['asc', 'desc'];
        
        if (in_array($sort, $validSortFields) && in_array($sortOrder, $validSortOrders)) {
            if ($sort === 'employee_name') {
                $query->orderBy('employee_name', $sortOrder);
            } else {
                $query->orderBy($sort, $sortOrder);
            }
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

        // Calculate monthly earnings summary for admin
        $monthlyJobsCompleted = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();
        
        // Calculate total earnings from payment_amount or total_due_cents
        $monthlyTotalEarnings = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->reduce(function($carry, $record) {
                // Use payment_amount if available, otherwise use total_due_cents
                $amount = $record->payment_amount ?? ($record->total_due_cents / 100);
                return $carry + $amount;
            }, 0);
        
        // Calculate payroll amounts paid out
        $monthlyPayrollPaid = $payrollRecords
            ->where('completed_at', '>=', now()->startOfMonth())
            ->where('payroll_status', 'paid')
            ->reduce(function($carry, $record) {
                return $carry + ($record->payroll_amount ?? 600);
            }, 0);
        
        // Admin earnings = total earnings - payroll amounts paid
        $monthlyEarnings = $monthlyTotalEarnings - $monthlyPayrollPaid;

        // Build payroll data for the payroll receipt modal
        // Use a composite key of booking_id + employee_id to handle multiple employees per booking
        $payrollData = [];
        foreach ($payrollRecords as $record) {
            $key = $record->booking_id . '_' . $record->employee_id;
            $payrollData[$key] = [
                'booking_code' => $record->booking_code,
                'completed_date' => $record->completed_at ? \Carbon\Carbon::parse($record->completed_at)->format('M j, Y') : 'N/A',
                'payroll_code' => $record->payroll_code,
                'payroll_amount' => $record->payroll_amount,
                'payroll_method' => $record->payroll_method,
                'payroll_status' => $record->payroll_status ?? 'unpaid',
                'payroll_proof' => $record->payroll_proof,
            ];
        }
        


        return view('admin.payroll', [
            'payrollRecords' => $payrollRecords,
            'serviceSummaries' => $serviceSummaries,
            'receiptData' => $receiptData,
            'payrollData' => $payrollData,
            'monthlyEarnings' => $monthlyEarnings,
            'monthlyTotalEarnings' => $monthlyTotalEarnings,
            'monthlyJobsCompleted' => $monthlyJobsCompleted,
            'search' => $search,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Upload payment proof for employee payroll
     */
    public function uploadPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'employee_id' => 'required|exists:employees,id',
            'payroll_amount' => 'required|numeric|min:0.01',
            'payroll_method' => 'required|in:cash,gcash,bank_transfer',
            'payroll_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        try {
            // Verify that the employee is assigned to this booking
            $isAssigned = DB::table('booking_staff_assignments')
                ->where('booking_id', $request->booking_id)
                ->where('employee_id', $request->employee_id)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee is not assigned to this booking.'
                ], 403);
            }

            // First, try to find an employee-specific payment proof record
            $employeePaymentProof = PaymentProof::where('booking_id', $request->booking_id)
                ->where('employee_id', $request->employee_id)
                ->where('status', 'approved')
                ->first();

            // If not found, look for any approved payment proof for this booking
            if (!$employeePaymentProof) {
                $employeePaymentProof = PaymentProof::where('booking_id', $request->booking_id)
                    ->where('status', 'approved')
                    ->first();
            }

            if (!$employeePaymentProof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof not found or not approved for this booking.'
                ], 404);
            }

            // Handle file upload
            $file = $request->file('payroll_proof');
            $filename = 'payroll_proof_' . $request->booking_id . '_' . $request->employee_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payroll_proofs', $filename, 'public');

            // Generate payroll code
            $payrollCode = PaymentProof::generatePayrollCode();

            // Check if this is an employee-specific payment proof or a shared one
            if ($employeePaymentProof->employee_id == $request->employee_id) {
                // Update the employee's own payment proof record
                $employeePaymentProof->update([
                    'payroll_code' => $payrollCode,
                    'payroll_status' => 'paid',
                    'payroll_amount' => $request->payroll_amount,
                    'payroll_proof' => $path,
                    'payroll_method' => $request->payroll_method,
                ]);
                // Notify employee and admin about payroll upload
                app(\App\Services\NotificationService::class)->notifyPayrollPaymentUploaded($employeePaymentProof->fresh());
            } else {
                // This is a shared payment proof, create a new record for this specific employee
                $newProof = PaymentProof::create([
                    'booking_id' => $request->booking_id,
                    'employee_id' => $request->employee_id,
                    'customer_id' => $employeePaymentProof->customer_id,
                    'image_path' => $employeePaymentProof->image_path,
                    'amount' => $employeePaymentProof->amount,
                    'payment_method' => $employeePaymentProof->payment_method,
                    'status' => 'approved',
                    'admin_notes' => $employeePaymentProof->admin_notes,
                    'reviewed_by' => $employeePaymentProof->reviewed_by,
                    'reviewed_at' => $employeePaymentProof->reviewed_at,
                    'uploaded_by' => $employeePaymentProof->uploaded_by,
                    'payroll_code' => $payrollCode,
                    'payroll_status' => 'paid',
                    'payroll_amount' => $request->payroll_amount,
                    'payroll_proof' => $path,
                    'payroll_method' => $request->payroll_method,
                ]);
                // Notify employee and admin about payroll upload using the specific record
                app(\App\Services\NotificationService::class)->notifyPayrollPaymentUploaded($newProof);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment uploaded successfully.',
                'payroll_code' => $payrollCode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
