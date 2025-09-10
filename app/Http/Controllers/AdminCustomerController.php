<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    public function index()
    {
        // List all users with role=customer, even if customers row not created yet
        $customers = DB::table('users')
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
                DB::raw("COALESCE(addr.city, '') as address_city"),
                DB::raw("COALESCE(addr.province, '') as address_province"),
            ])
            ->selectSub(function ($q) {
                $q->from('bookings as b')
                  ->whereColumn('b.customer_id', 'customers.id')
                  ->selectRaw('count(*)');
            }, 'bookings_count')
            ->orderBy('customers.id', 'asc')
            ->paginate(15);

        return view('admin.customers', compact('customers'));
    }
}

?>


