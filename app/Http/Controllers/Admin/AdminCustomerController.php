<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        // Get search and sort parameters
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'customer_id'); // 'customer_id' or 'name'
        $sortOrder = $request->get('sort_order', 'asc'); // 'asc' or 'desc'
        
        // Build the base query
        $query = DB::table('users')
            ->leftJoin('customers', 'customers.user_id', '=', 'users.id')
            ->leftJoin('addresses as addr', 'addr.id', '=', 'customers.default_address_id')
            ->where('users.role', 'customer')
            ->select([
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.username',
                'users.phone',
                'customers.id as customer_id',
                'customers.customer_code',
                DB::raw("COALESCE(addr.line1, '') as address_line1"),
                DB::raw("COALESCE(addr.barangay, '') as address_barangay"),
                DB::raw("COALESCE(addr.city, '') as address_city"),
                DB::raw("COALESCE(addr.province, '') as address_province"),
            ])
            ->selectSub(function ($q) {
                $q->from('bookings as b')
                  ->whereColumn('b.customer_id', 'customers.id')
                  ->selectRaw('count(*)');
            }, 'bookings_count');

        // Apply search logic - search across all relevant fields
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%")
                  ->orWhere('customers.customer_code', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply sorting with order
        if ($sort === 'name') {
            $query->orderBy('users.first_name', $sortOrder)
                  ->orderBy('users.last_name', $sortOrder);
        } else {
            // Default sort by customer_id
            $query->orderBy('customers.id', $sortOrder);
        }

        $customers = $query->paginate(15);

        return view('admin.customers', compact('customers', 'search', 'sort', 'sortOrder'));
    }

    /**
     * Delete a customer (remove user from users table)
     * This will permanently delete the customer and all their data
     */
    public function destroy($userId)
    {
        try {
            $user = DB::table('users')->where('id', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            // Ensure this is a customer
            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a customer'
                ], 400);
            }

            // Get customer record
            $customer = DB::table('customers')->where('user_id', $userId)->first();

            // Store customer details for notification before deletion
            $customerName = $user->first_name . ' ' . $user->last_name;
            $customerCode = $customer->customer_code ?? 'N/A';
            $username = $user->username;

            // Check if customer has any active bookings
            if ($customer) {
                $activeBookings = DB::table('bookings')
                    ->where('customer_id', $customer->id)
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->count();

                if ($activeBookings > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete customer with active bookings. Please complete or cancel their bookings first.'
                    ], 400);
                }

                // Check if customer has any pending payments
                $pendingPayments = DB::table('bookings')
                    ->where('customer_id', $customer->id)
                    ->where('status', 'completed')
                    ->where('payment_status', 'pending')
                    ->count();

                if ($pendingPayments > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete customer with pending payments. Please resolve all payment issues first.'
                    ], 400);
                }
            }

            // Delete related records first (cascade delete)
            if ($customer) {
                // Delete customer addresses
                DB::table('addresses')
                    ->where('user_id', $userId)
                    ->delete();
                
                // Delete customer bookings (completed ones only for safety)
                DB::table('bookings')
                    ->where('customer_id', $customer->id)
                    ->whereIn('status', ['completed', 'cancelled'])
                    ->delete();
                
                // Delete customer record
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->delete();
            }

            // Delete the user
            DB::table('users')
                ->where('id', $userId)
                ->delete();

            // Trigger notification for customer deletion
            $this->notificationService->notifyCustomerDeleted($customerName, $customerCode, $username);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer: ' . $e->getMessage()
            ], 500);
        }
    }
}

?>


