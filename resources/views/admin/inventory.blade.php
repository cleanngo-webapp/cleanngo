@extends('layouts.admin')

@section('title','Inventory')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Inventory</h1>

    <div class="mt-6 bg-white rounded-xl border">
        <div class="grid grid-cols-9 text-sm font-semibold">
            <div class="p-2">Item ID</div>
            <div class="p-2">Item Name</div>
            <div class="p-2">Category</div>
            <div class="p-2">Quantity</div>
            <div class="p-2">Re-Order Level</div>
            <div class="p-2">Status</div>
            <div class="p-2">Last Updated</div>
            <div class="p-2">Actions</div>
            <div class="p-2"></div>
        </div>
        <div class="grid grid-cols-9 text-sm">
            <div class="p-2">T101</div>
            <div class="p-2">Mop</div>
            <div class="p-2">Tool</div>
            <div class="p-2">15</div>
            <div class="p-2">10</div>
            <div class="p-2">Low Stock</div>
            <div class="p-2">Sept 10</div>
            <div class="p-2">...</div>
            <div class="p-2"></div>
        </div>
        <div class="p-4 text-xs text-gray-600">
            System auto-handles: reorder alerts, status computation, low-stock notifications.
        </div>
    </div>
</div>
@endsection


