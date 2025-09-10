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
            'label' => 'nullable|string|max:100',
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
            Address::create($data);
        });

        return redirect()->route('preview.customer')->with('status', 'Address saved');
    }

    public function destroy(Address $address)
    {
        $user = Auth::user();
        abort_if($address->user_id !== $user->id, 403);
        $address->delete();
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


