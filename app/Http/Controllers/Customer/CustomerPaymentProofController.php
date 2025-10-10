<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerPaymentProofController extends Controller
{
    /**
     * Upload payment proof for a booking (Customer version)
     * Allows customers to upload payment proof for their bookings
     */
    public function upload(Request $request, $bookingId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,gcash',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer authentication required.'
            ], 401);
        }

        // Get customer ID
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.'
            ], 404);
        }

        // Check if booking exists and belongs to this customer
        $booking = DB::table('bookings as b')
            ->where('b.id', $bookingId)
            ->where('b.customer_id', $customerId)
            ->whereIn('b.status', ['pending', 'confirmed', 'in_progress'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you do not have permission to upload payment proof for this booking.'
            ], 404);
        }

        // Check if there's already a pending or approved payment proof
        $existingProof = PaymentProof::where('booking_id', $bookingId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingProof) {
            return response()->json([
                'success' => false,
                'message' => 'A payment proof has already been uploaded for this booking and is ' . $existingProof->status . '.'
            ], 400);
        }

        // Handle file upload
        $file = $request->file('proof_image');
        $filename = 'customer_payment_proof_' . $bookingId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        // Create payment proof record
        PaymentProof::create([
            'booking_id' => $bookingId,
            'customer_id' => $customerId, // Store customer ID for customer uploads
            'image_path' => $path,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'uploaded_by' => 'customer', // Track who uploaded the proof
        ]);

        // Return JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully! Waiting for admin approval.',
            'booking_id' => $bookingId
        ]);
    }

    /**
     * Get payment proof details for a booking (Customer view)
     */
    public function getDetails($bookingId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer authentication required.'
            ], 401);
        }

        // Get customer ID
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.'
            ], 404);
        }

        // Check if booking belongs to this customer
        $booking = DB::table('bookings as b')
            ->where('b.id', $bookingId)
            ->where('b.customer_id', $customerId)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or access denied.'
            ], 404);
        }

        // Get payment proof for this booking
        $proof = PaymentProof::where('booking_id', $bookingId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$proof) {
            return response()->json([
                'success' => false,
                'message' => 'No payment proof found for this booking.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'proof' => [
                'id' => $proof->id,
                'amount' => $proof->amount,
                'payment_method' => $proof->payment_method,
                'status' => $proof->status,
                'admin_notes' => $proof->admin_notes,
                'image_url' => asset('storage/' . $proof->image_path),
                'uploaded_by' => $proof->uploaded_by,
                'created_at' => $proof->created_at->format('M j, Y g:i A'),
                'reviewed_at' => $proof->reviewed_at?->format('M j, Y g:i A'),
                'booking_id' => $booking->id,
                'booking_code' => $booking->code,
            ]
        ]);
    }
}
