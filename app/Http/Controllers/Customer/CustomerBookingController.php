<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
            'items_json' => 'nullable|string',
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

        // Generate BYYYYXXX code and store booking
        $code = 'B'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT);
        while (DB::table('bookings')->where('code',$code)->exists()) {
            $code = 'B'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT);
        }
        $bookingId = DB::table('bookings')->insertGetId([
            'code' => $code,
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

        // Persist booking items if provided
        $items = [];
        if (!empty($request->items_json)) {
            $decoded = json_decode($request->items_json, true);
            if (is_array($decoded)) { $items = $decoded; }
        }
        foreach ($items as $item) {
            $qty = (int)($item['qty'] ?? 0);
            $unit = (int)($item['unitPrice'] ?? 0);
            $sqm = isset($item['areaSqm']) ? (float)$item['areaSqm'] : null;
            $line = (int)round(($sqm ? $sqm * $qty * $unit : $qty * $unit) * 100 / 100); // pesos to cents handled by inputs
            DB::table('booking_items')->insert([
                'booking_id' => $bookingId,
                'service_id' => $serviceId,
                'item_type' => $item['type'] ?? null,
                'quantity' => $qty,
                'area_sqm' => $sqm,
                'unit_price_cents' => ($unit * 100),
                'line_total_cents' => ($line * 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('status','Booking successful!');
    }

    /**
     * Cancel a booking (only if status is pending)
     * This method allows customers to cancel their own pending bookings
     */
    public function cancel(Request $request, $bookingId)
    {
        $user = Auth::user();
        
        // Get the booking and verify ownership
        $booking = DB::table('bookings as b')
            ->join('customers as c', 'c.id', '=', 'b.customer_id')
            ->where('b.id', $bookingId)
            ->where('c.user_id', $user->id)
            ->where('b.status', 'pending') // Only allow cancellation of pending bookings
            ->select('b.*')
            ->first();

        if (!$booking) {
            return back()->withErrors(['cancel' => 'Booking not found or cannot be cancelled.']);
        }

        // Update booking status to cancelled
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_reason' => 'Cancelled by customer',
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Booking cancelled successfully.');
    }
}

?>


