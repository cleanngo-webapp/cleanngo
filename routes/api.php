<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Primary address lookup for admin map modal
Route::get('/user/{userId}/primary-address', function ($userId) {
    $addr = DB::table('addresses as a')
        ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
        ->where('a.user_id', $userId)
        ->orderByDesc('a.is_primary')
        ->orderBy('a.id')
        ->first(['a.line1','a.barangay','a.city','a.province','a.latitude','a.longitude','u.phone']);
    return response()->json($addr);
});
