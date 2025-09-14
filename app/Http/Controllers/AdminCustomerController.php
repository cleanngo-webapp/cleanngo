<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
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
}

?>


