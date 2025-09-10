<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class CustomerHomeController extends Controller
{
    public function home()
    {
        return view('customer.home');
    }
}

?>


