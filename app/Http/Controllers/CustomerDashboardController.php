<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Get customer ID
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        
        // Fetch customer addresses
        $addresses = DB::table('addresses')
            ->where('user_id', $user->id)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        // Fetch customer bookings with related data
        $bookings = collect();
        if ($customerId) {
            $bookings = DB::table('bookings as b')
                ->leftJoin('services as s', 's.id', '=', 'b.service_id')
                ->leftJoin('addresses as a', 'a.id', '=', 'b.address_id')
                ->leftJoin('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
                ->leftJoin('employees as e', 'e.id', '=', 'bsa.employee_id')
                ->leftJoin('users as eu', 'eu.id', '=', 'e.user_id')
                ->where('b.customer_id', $customerId)
                ->select([
                    'b.id', 'b.code', 'b.scheduled_start', 'b.scheduled_end', 
                    'b.status', 'b.payment_status', 'b.total_due_cents',
                    's.name as service_name', 's.description as service_description',
                    DB::raw("CONCAT(a.line1, ', ', COALESCE(a.barangay, ''), ', ', COALESCE(a.city, ''), ', ', COALESCE(a.province, '')) as full_address"),
                    DB::raw("CONCAT(eu.first_name, ' ', eu.last_name) as employee_name"),
                    'b.created_at'
                ])
                ->orderByDesc('b.scheduled_start')
                ->get();
        }

        return view('customer.profile', [
            'addresses' => $addresses,
            'bookings' => $bookings,
        ]);
    }
}

?>


