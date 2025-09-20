<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentProofController extends Controller
{
    /**
     * Upload payment proof for a booking
     */
    public function upload(Request $request, $bookingId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,gcash',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return back()->withErrors(['error' => 'Employee not found.']);
        }

        // Check if booking exists and is assigned to this employee
        $booking = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->where('b.id', $bookingId)
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', 'in_progress')
            ->first();

        if (!$booking) {
            return back()->withErrors(['error' => 'Booking not found or not assigned to you.']);
        }

        // Handle file upload
        $file = $request->file('proof_image');
        $filename = 'payment_proof_' . $bookingId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        // Create payment proof record
        PaymentProof::create([
            'booking_id' => $bookingId,
            'employee_id' => $employeeId,
            'image_path' => $path,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully! Waiting for admin approval.',
                'booking_id' => $bookingId
            ]);
        }
        
        // Return redirect for regular form submissions
        return back()->with('status', 'Payment proof uploaded successfully. Waiting for admin approval.');
    }

    /**
     * Admin approve payment proof
     */
    public function approve(Request $request, $proofId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $proof = PaymentProof::findOrFail($proofId);
        
        $proof->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update the booking's payment status and payment method when payment proof is approved
        DB::table('bookings')->where('id', $proof->booking_id)->update([
            'payment_status' => 'paid',
            'payment_method' => $proof->payment_method,
            'amount_paid_cents' => $proof->amount * 100, // Convert to cents
            'updated_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment proof approved successfully!',
                'proof_id' => $proofId
            ]);
        }
        
        // Return redirect for regular form submissions
        return back()->with('status', 'Payment proof approved successfully.');
    }

    /**
     * Admin decline payment proof
     */
    public function decline(Request $request, $proofId)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $proof = PaymentProof::findOrFail($proofId);
        
        $proof->update([
            'status' => 'declined',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment proof declined successfully!',
                'proof_id' => $proofId
            ]);
        }
        
        // Return redirect for regular form submissions
        return back()->with('status', 'Payment proof declined. Employee can upload a new one.');
    }

    /**
     * Get payment proof details for modal
     */
    public function getDetails($proofId)
    {
        $proof = PaymentProof::with(['employee.user', 'reviewer'])
            ->findOrFail($proofId);

        return response()->json([
            'id' => $proof->id,
            'amount' => $proof->amount,
            'payment_method' => $proof->payment_method,
            'status' => $proof->status,
            'admin_notes' => $proof->admin_notes,
            'image_url' => Storage::url($proof->image_path),
            'employee_name' => $proof->employee->user->first_name . ' ' . $proof->employee->user->last_name,
            'reviewed_by' => $proof->reviewer ? $proof->reviewer->first_name . ' ' . $proof->reviewer->last_name : null,
            'reviewed_at' => $proof->reviewed_at?->format('M j, Y g:i A'),
            'created_at' => $proof->created_at->format('M j, Y g:i A'),
        ]);
    }
}