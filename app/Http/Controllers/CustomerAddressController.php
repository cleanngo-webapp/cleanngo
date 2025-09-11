<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'label' => 'nullable|in:home,office',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_primary' => 'sometimes|boolean',
        ]);

        $data['user_id'] = $user->id;

        DB::transaction(function () use ($data, $user) {
            if (!empty($data['is_primary'])) {
                DB::table('addresses')->where('user_id', $user->id)->update(['is_primary' => false]);
            }
            try {
                Address::create($data);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle possible sequence desync in Postgres (duplicate key on addresses_pkey)
                $message = $e->getMessage();
                $code = $e->getCode();
                if ($code === '23505' && (str_contains($message, 'addresses_pkey') || str_contains($message, 'duplicate key'))) {
                    // Reseed sequence to max(id)+1 and retry
                    DB::statement("SELECT setval(pg_get_serial_sequence('addresses','id'), COALESCE((SELECT MAX(id) FROM addresses), 0) + 1, false)");
                    Address::create($data);
                } else {
                    throw $e;
                }
            }
        });

        return redirect()->route('customer.profile')->with('status', 'Address saved');
    }

    public function destroy(Address $address)
    {
        $user = Auth::user();
        abort_if($address->user_id !== $user->id, 403);
        // Check if any bookings still reference this address
        $bookingsCount = DB::table('bookings')->where('address_id', $address->id)->count();
        // Find an alternative address for potential reassignment
        $alternative = Address::where('user_id', $user->id)
            ->where('id', '!=', $address->id)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->first();

        // If this address is referenced by bookings and there is no alternative, block deletion
        if ($bookingsCount > 0 && !$alternative) {
            return back()->with('status', 'Cannot delete this address because it is used by existing bookings. Please add another address first.');
        }

        DB::transaction(function () use ($address, $user, $bookingsCount, $alternative) {
            // Reassign bookings to an alternative address, if needed
            if ($bookingsCount > 0 && $alternative) {
                DB::table('bookings')->where('address_id', $address->id)->update(['address_id' => $alternative->id]);
            }

            $wasPrimary = (bool) $address->is_primary;
            $address->delete();

            if ($wasPrimary) {
                // Pick a replacement address for this user and mark it primary
                $replacement = Address::where('user_id', $user->id)
                    ->orderByDesc('created_at')
                    ->first();
                if ($replacement) {
                    // Ensure only this one is primary
                    DB::table('addresses')->where('user_id', $user->id)->update(['is_primary' => false]);
                    $replacement->update(['is_primary' => true]);
                }
            }
        });
        return back()->with('status', 'Address removed');
    }

    public function setPrimary(Address $address)
    {
        $user = Auth::user();
        abort_if($address->user_id !== $user->id, 403);
        DB::transaction(function () use ($address, $user) {
            DB::table('addresses')->where('user_id', $user->id)->update(['is_primary' => false]);
            $address->update(['is_primary' => true]);
        });
        return back()->with('status', 'Primary address updated');
    }
}

?>


