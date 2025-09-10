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

    <div class="mt-6 bg-white rounded-xl border overflow-x-auto">
        <div class="p-3 font-semibold text-center">Customer Records Table</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left">
                    <th class="px-3 py-2">Customer ID</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Contact</th>
                    <th class="px-3 py-2">Address</th>
                    <th class="px-3 py-2">Bookings</th>
                    <th class="px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $cust)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $cust->customer_code ?? ($cust->customer_id ? sprintf('C%04d%03d', date('Y'), $cust->customer_id % 1000) : '—') }}</td>
                        <td class="px-3 py-2">{{ trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '')) ?: $cust->username }}</td>
                        <td class="px-3 py-2">{{ $cust->phone ?? '—' }}</td>
                        <td class="px-3 py-2">
                            @php
                                $city = $cust->address_city ? (', ' . $cust->address_city) : '';
                                $province = $cust->address_province ? (', ' . $cust->address_province) : '';
                            @endphp
                            {{ ($cust->address_line1 ?: '—') . ($cust->address_line1 ? $city . $province : '') }}
                        </td>
                        <td class="px-3 py-2">{{ $cust->bookings_count ?? 0 }}</td>
                        <td class="px-3 py-2">
                            <a href="#" class="text-emerald-700 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @isset($customers)
            <div class="p-3">{{ $customers->links() }}</div>
        @endisset
    </div>
</div>
@endsection


