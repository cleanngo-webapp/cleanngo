<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'booking_photos' => 'required|array|min:1|max:3',
            'booking_photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ensure customer row exists
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'customer_code' => 'C'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT),
            ]);
        }

        // Get the primary service ID (will be updated based on items)
        $service = Service::where('name','Sofa Mattress Deep Cleaning')->first();
        if (!$service) {
            $service = Service::where('name','General')->first();
        }

        $start = \Carbon\Carbon::parse($request->date.' '.$request->time);
        $totalCents = (int) round($request->total * 100);

        // Generate BYYYYXXX code
        $code = 'B'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT);
        while (Booking::where('code',$code)->exists()) {
            $code = 'B'.date('Y').str_pad((string)random_int(0,999),3,'0',STR_PAD_LEFT);
        }
        
        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('booking_photos')) {
            foreach ($request->file('booking_photos') as $photo) {
                // Generate unique filename
                $filename = 'booking_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                
                // Store the photo in public/booking-photos directory
                $path = $photo->storeAs('booking-photos', $filename, 'public');
                $photoPaths[] = $path;
            }
        }

        // Create booking using Eloquent model (this will trigger the notification)
        $booking = Booking::create([
            'code' => $code,
            'customer_id' => $customer->id,
            'address_id' => $request->address_id,
            'service_id' => $service->id,
            'scheduled_start' => $start,
            'status' => 'pending',
            'base_price_cents' => $totalCents,
            'total_due_cents' => $totalCents,
            'booking_photos' => $photoPaths,
        ]);

        // Persist booking items if provided
        $items = [];
        if (!empty($request->items_json)) {
            $decoded = json_decode($request->items_json, true);
            if (is_array($decoded)) { $items = $decoded; }
        }
        
        // Map item types to service IDs
        $serviceMapping = [
        
            // Sofa items (updated to match new dropdown values)
            'sofa_1' => 'Sofa Mattress Deep Cleaning',
            'sofa_2' => 'Sofa Mattress Deep Cleaning',
            'sofa_3' => 'Sofa Mattress Deep Cleaning',
            'sofa_4' => 'Sofa Mattress Deep Cleaning',
            'sofa_5' => 'Sofa Mattress Deep Cleaning',
            'sofa_6' => 'Sofa Mattress Deep Cleaning',
            'sofa_7' => 'Sofa Mattress Deep Cleaning',
            'sofa_8' => 'Sofa Mattress Deep Cleaning',
            
            // Mattress items
            'mattress_single' => 'Mattress Deep Cleaning',
            'mattress_double' => 'Mattress Deep Cleaning',
            'mattress_king' => 'Mattress Deep Cleaning',
            'mattress_california' => 'Mattress Deep Cleaning',
            
            // Car items
            'car_sedan' => 'Home Service Car Interior Detailing',
            'car_suv' => 'Home Service Car Interior Detailing',
            'car_van' => 'Home Service Car Interior Detailing',
            'car_coaster' => 'Home Service Car Interior Detailing',
            
            // Area-based services (updated names and added new services)
            'carpet_sqft' => 'Carpet Deep Cleaning',
            'post_construction_sqm' => 'Post Construction Cleaning',
            'disinfect_sqm' => 'Home/Office Disinfection',
            'glass_sqft' => 'Glass Cleaning',
            'house_cleaning_sqm' => 'House Cleaning',
            'curtain_cleaning_yard' => 'Curtain Cleaning',
        ];
        
        foreach ($items as $item) {
            $qty = (int)($item['qty'] ?? 0);
            $unit = (int)($item['unitPrice'] ?? 0);
            $sqm = isset($item['areaSqm']) ? (float)$item['areaSqm'] : null;
            $line = (int)round(($sqm ? $sqm * $qty * $unit : $qty * $unit) * 100 / 100); // pesos to cents handled by inputs
            
            // Get the correct service ID for this item type
            $itemType = $item['type'] ?? null;
            $serviceName = $serviceMapping[$itemType] ?? 'General';
            $itemService = Service::where('name', $serviceName)->first();
            
            // Fallback to General service if specific service not found
            if (!$itemService) {
                $itemService = Service::where('name', 'General')->first();
            }
            
            // Create booking item using Eloquent model
            BookingItem::create([
                'booking_id' => $booking->id,
                'service_id' => $itemService->id,
                'item_type' => $itemType,
                'quantity' => $qty,
                'area_sqm' => $sqm,
                'unit_price_cents' => ($unit * 100),
                'line_total_cents' => ($line * 100),
            ]);
        }

        // Trigger notification after all booking items are created
        $booking->triggerBookingCreatedNotification();

        // Return JSON response for AJAX requests (modal submissions)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_code' => $booking->code
            ]);
        }
        
        // Return redirect for regular form submissions
        return back()->with('status','Booking successful!');
    }

    /**
     * Cancel a booking (only if status is pending)
     * This method allows customers to cancel their own pending bookings
     */
    public function cancel(Request $request, $bookingId)
    {
        $user = Auth::user();
        
        // Get the booking and verify ownership using Eloquent model
        $booking = Booking::whereHas('customer', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('id', $bookingId)
        ->where('status', 'pending') // Only allow cancellation of pending bookings
        ->first();

        if (!$booking) {
            return back()->withErrors(['cancel' => 'Booking not found or cannot be cancelled.']);
        }

        // Update booking status to cancelled using Eloquent model to trigger notifications
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->cancelled_reason = 'Cancelled by customer';
        $booking->save(); // This will trigger the boot() method and send notifications

        return back()->with('status', 'Booking cancelled successfully.');
    }
}

?>


