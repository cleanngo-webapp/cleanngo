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
    $addr = DB::table('addresses')
        ->where('user_id', $userId)
        ->orderByDesc('is_primary')
        ->orderBy('id')
        ->first(['line1','city','province','latitude','longitude']);
    return response()->json($addr);
});
