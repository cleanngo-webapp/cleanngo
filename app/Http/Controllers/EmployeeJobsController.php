<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeJobsController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            $empty = DB::table('bookings')->whereRaw('1=0')->paginate(15);
            return view('employee.jobs', [
                'bookings' => $empty,
                'locationsData' => [],
                'receiptData' => [],
            ]);
        }

        $bookings = DB::table('bookings as b')
            ->leftJoin('customers as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('booking_staff_assignments as a', 'a.booking_id', '=', 'b.id')
            ->leftJoin('addresses as primary_addr', 'primary_addr.id', '=', 'c.default_address_id')
            ->where('a.employee_id', $employeeId)
            ->orderByDesc('b.scheduled_start')
            ->select([
                'b.id', 'b.code', 'b.status', 'b.scheduled_start',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw('u.phone as customer_phone'),
                DB::raw("COALESCE(primary_addr.line1,'') as address_line1"),
                DB::raw("COALESCE(primary_addr.barangay,'') as address_barangay"),
                DB::raw("COALESCE(primary_addr.city,'') as address_city"),
                DB::raw("COALESCE(primary_addr.province,'') as address_province"),
                'primary_addr.latitude', 'primary_addr.longitude',
            ])
            ->paginate(15);

        // Build map payload
        $locationsData = collect($bookings->items())->mapWithKeys(function ($b) {
            $parts = array_filter([$b->address_line1 ?? null, $b->address_barangay ?? null, $b->address_city ?? null, $b->address_province ?? null]);
            return [
                $b->id => [
                    'address' => implode(', ', $parts),
                    'lat' => $b->latitude,
                    'lng' => $b->longitude,
                    'phone' => $b->customer_phone,
                ]
            ];
        })->all();

        // Build simple receipt payload similar to admin
        $receiptData = [];
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $group = [];
            foreach ($rows as $r) {
                $group[$r->booking_id][] = $r;
            }
            foreach ($group as $bid => $items) {
                $total = 0.0; $lines = [];
                foreach ($items as $it) {
                    $amt = ($it->line_total_cents ?? 0) / 100.0; $total += $amt;
                    $label = trim(($it->item_type ?? 'item') . ' x ' . (int)($it->quantity ?? 0) . ($it->area_sqm ? (' @ '.$it->area_sqm.' sqm') : ''));
                    $lines[] = ['label' => $label, 'amount' => $amt];
                }
                $receiptData[$bid] = ['lines' => $lines, 'total' => $total];
            }
        }

        return view('employee.jobs', [
            'bookings' => $bookings,
            'locationsData' => $locationsData,
            'receiptData' => $receiptData,
        ]);
    }

    public function start(Request $request, int $bookingId)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) { return back(); }
        // Ensure this employee is assigned to the booking
        $assigned = DB::table('booking_staff_assignments')
            ->where('booking_id', $bookingId)
            ->where('employee_id', $employeeId)
            ->exists();
        if (!$assigned) { return back(); }
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => 'in_progress',
            'updated_at' => now(),
        ]);
        return back()->with('status', 'Job started');
    }

    public function complete(Request $request, int $bookingId)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) { return back(); }
        $assigned = DB::table('booking_staff_assignments')
            ->where('booking_id', $bookingId)
            ->where('employee_id', $employeeId)
            ->exists();
        if (!$assigned) { return back(); }
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => 'completed',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('status', 'Job marked as completed');
    }
}


