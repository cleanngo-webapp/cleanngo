<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $addresses = DB::table('addresses')
            ->where('user_id', $user->id)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        return view('customer.profile', [
            'addresses' => $addresses,
        ]);
    }
}

?>


