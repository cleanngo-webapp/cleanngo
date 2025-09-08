@extends('layouts.admin')

@section('title','Customers')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Customers</h1>

    <div class="mt-6 flex items-center gap-4">
        <input type="text" placeholder="Search" class="border rounded px-3 py-2 w-80">
        <div class="ml-auto text-sm">
            <button class="px-3 py-2 bg-emerald-700 text-white rounded">Filter by Date</button>
            <button class="px-3 py-2 bg-emerald-700 text-white rounded ml-2">Filter by Service</button>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-xl border">
        <div class="p-3 font-semibold text-center">Employee Records Table</div>
        <div class="grid grid-cols-6 text-sm font-semibold">
            <div class="p-2">CustomerID</div>
            <div class="p-2">Name</div>
            <div class="p-2">Contact</div>
            <div class="p-2">Address</div>
            <div class="p-2">Bookings</div>
            <div class="p-2">Actions</div>
        </div>
        <div class="grid grid-cols-6 text-sm">
            <div class="p-2">C001</div>
            <div class="p-2">—</div>
            <div class="p-2">—</div>
            <div class="p-2">—</div>
            <div class="p-2">—</div>
            <div class="p-2">...</div>
        </div>
    </div>
</div>
@endsection


