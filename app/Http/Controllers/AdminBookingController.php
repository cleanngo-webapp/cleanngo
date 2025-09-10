<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = DB::table('bookings as b')
            ->leftJoin('customers as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('services as s', 's.id', '=', 'b.service_id')
            ->leftJoin('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->leftJoin('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->leftJoin('users as eu', 'eu.id', '=', 'e.user_id')
            ->leftJoin('addresses as a', 'a.id', '=', 'b.address_id')
            ->select([
                'b.id', 'b.code', 'b.scheduled_start', 'b.status', 'b.address_id',
                's.name as service_name',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw("CONCAT(eu.first_name,' ',eu.last_name) as employee_name"),
                DB::raw('e.user_id as employee_user_id'),
                DB::raw('bsa.employee_id as assigned_employee_id'),
                DB::raw("COALESCE(a.line1,'') as address_line1"),
                DB::raw("COALESCE(a.city,'') as address_city"),
                DB::raw("COALESCE(a.province,'') as address_province"),
                DB::raw('a.latitude as address_latitude'),
                DB::raw('a.longitude as address_longitude'),
            ])
            ->orderByDesc('b.scheduled_start')
            ->paginate(15);

        // For modal dropdowns
        $customers = DB::table('users')->where('role','customer')->orderBy('first_name')->orderBy('last_name')->get(['id','first_name','last_name']);
        $employees = DB::table('users')->where('role','employee')->orderBy('first_name')->orderBy('last_name')->get(['id','first_name','last_name']);

        // Pull booking item summaries and detailed lines for receipts (DB-agnostic, aggregate in PHP)
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        $itemsByBooking = collect();
        $receiptData = [];
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                // Summary label
                $label = trim(($r->item_type ?? 'item') . ' x ' . (int)($r->quantity ?? 0));
                $itemsByBooking[$r->booking_id] = isset($itemsByBooking[$r->booking_id])
                    ? ($itemsByBooking[$r->booking_id] . ', ' . $label)
                    : $label;
                // Detailed lines
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
                foreach ($lines as $ln) { $total += (float)($ln['line_total'] ?? 0); }
                $receiptData[$bid] = [ 'lines' => $lines, 'total' => $total ];
            }
        }

        // Build locations payload for map modal
        $locationsData = collect($bookings->items())->mapWithKeys(function($b){
            $addrParts = array_filter([$b->address_line1 ?? null, $b->address_city ?? null, $b->address_province ?? null]);
            return [
                $b->id => [
                    'address' => implode(', ', $addrParts),
                    'lat' => $b->address_latitude,
                    'lng' => $b->address_longitude,
                ]
            ];
        })->all();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'customers' => $customers,
            'employees' => $employees,
            'itemSummaries' => $itemsByBooking,
            'locationsData' => $locationsData,
            'receiptData' => $receiptData,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_user_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'summary' => 'nullable|string',
        ]);

        $customerId = DB::table('customers')->where('user_id', $data['user_id'])->value('id');
        if (!$customerId) {
            // Create minimal customer row if missing
            $customerId = DB::table('customers')->insertGetId([
                'user_id' => $data['user_id'],
                'customer_code' => $this->generateCode('C'),
            ]);
        }

        $addressId = DB::table('addresses')
            ->where('user_id', $data['user_id'])
            ->orderByDesc('is_primary')->orderBy('id')->value('id');

        $start = \Carbon\Carbon::parse($data['date'].' '.$data['time']);
        $code = $this->generateCode('B');
        $bookingId = DB::table('bookings')->insertGetId([
            'code' => $code,
            'customer_id' => $customerId,
            'address_id' => $addressId,
            'service_id' => DB::table('services')->where('name','General')->value('id') ?? DB::table('services')->insertGetId([
                'name' => 'General', 'description' => 'Manual entry', 'base_price_cents' => 0, 'duration_minutes' => 60, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()
            ]),
            'scheduled_start' => $start,
            'status' => 'pending',
            'notes' => $data['summary'] ?? null,
            'base_price_cents' => 0,
            'total_due_cents' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!empty($data['employee_user_id'])) {
            $employeeId = DB::table('employees')->where('user_id', $data['employee_user_id'])->value('id');
            if ($employeeId) {
                DB::table('booking_staff_assignments')->updateOrInsert(
                    ['booking_id' => $bookingId, 'employee_id' => $employeeId],
                    ['role' => 'cleaner', 'assigned_at' => now(), 'assigned_by' => Auth::id()]
                );
            }
        }

        return back()->with('status', 'Booking created');
    }

    public function updateStatus(Request $request, $bookingId)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,confirmed,cancelled,completed']);
        if ($request->status === 'cancelled') {
            // Delete the booking when cancelled as requested
            DB::table('booking_staff_assignments')->where('booking_id', $bookingId)->delete();
            DB::table('booking_items')->where('booking_id', $bookingId)->delete();
            DB::table('bookings')->where('id', $bookingId)->delete();
            return back()->with('status','Booking cancelled and removed');
        }
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);
        return back();
    }

    public function assignEmployee(Request $request, $bookingId)
    {
        $request->validate(['employee_user_id' => 'required|exists:users,id']);
        $employeeId = DB::table('employees')->where('user_id', $request->employee_user_id)->value('id');
        if ($employeeId) {
            // Do not allow reassignment once any employee is assigned to this booking
            $alreadyAssigned = DB::table('booking_staff_assignments')->where('booking_id', $bookingId)->exists();
            if ($alreadyAssigned) {
                return back()->withErrors(['assign' => 'An employee is already assigned to this booking and cannot be changed.']);
            }
            DB::table('booking_staff_assignments')->insert([
                'booking_id'   => $bookingId,
                'employee_id'  => $employeeId,
                'role'         => 'cleaner',
                'assigned_at'  => now(),
                'assigned_by'  => Auth::id(),
            ]);
        }
        return back()->with('status', 'Employee assigned.');
    }

    private function generateCode(string $prefix): string
    {
        $year = date('Y');
        for ($i=0; $i<1000; $i++) {
            $code = $prefix.$year.str_pad((string)random_int(0,999), 3, '0', STR_PAD_LEFT);
            $exists = $prefix==='B' ? DB::table('bookings')->where('code',$code)->exists() : DB::table('customers')->where('customer_code',$code)->exists();
            if (!$exists) return $code;
        }
        return $prefix.$year.substr((string)microtime(true), -3);
    }
}

?>


