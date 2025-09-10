<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerBookingController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'date' => 'required|date',
            'time' => 'required',
            'total' => 'required|numeric|min:0',
        ]);

        // Ensure customer row exists
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        if (!$customerId) {
            $customerId = DB::table('customers')->insertGetId([
                'user_id' => $user->id,
                'customer_code' => 'C'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT),
            ]);
        }

        // For now, create a generic service entry if needed
        $serviceId = DB::table('services')->where('name','General')->value('id');
        if (!$serviceId) {
            $serviceId = DB::table('services')->insertGetId([
                'name' => 'General',
                'description' => 'Generated booking',
                'base_price_cents' => 0,
                'duration_minutes' => 60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $start = \Carbon\Carbon::parse($request->date.' '.$request->time);
        $totalCents = (int) round($request->total * 100);

        $bookingId = DB::table('bookings')->insertGetId([
            'code' => 'B'.date('Ymd').str_pad((string)random_int(0,9999),4,'0',STR_PAD_LEFT),
            'customer_id' => $customerId,
            'address_id' => $request->address_id,
            'service_id' => $serviceId,
            'scheduled_start' => $start,
            'status' => 'pending',
            'base_price_cents' => $totalCents,
            'total_due_cents' => $totalCents,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status','Booking successful!');
    }
}

?>


