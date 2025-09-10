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
                            <button class="text-emerald-700 hover:underline" onclick="window.dispatchEvent(new CustomEvent('showCustomerMap',{detail:{userId:{{ $cust->user_id }}}}))">View</button>
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
    <div id="customer-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Customer Address</div>
                <button onclick="hideCustMap()">✕</button>
            </div>
            <div id="customerMap" class="h-80 rounded border"></div>
        </div>
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    var custMap, custMarker;
    function hideCustMap(){ document.getElementById('customer-map-modal').classList.add('hidden'); }
    window.addEventListener('showCustomerMap', async function(e){
        const userId = e.detail.userId;
        // fetch primary address for the customer
        const res = await fetch('/api/user/'+userId+'/primary-address');
        const data = await res.json();
        document.getElementById('customer-map-modal').classList.remove('hidden');
        setTimeout(function(){
            if(!custMap){
                custMap = L.map('customerMap').setView([14.5995,120.9842], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(custMap);
            }
            var lat = data?.latitude ?? 14.5995, lng = data?.longitude ?? 120.9842;
            if(!custMarker){ custMarker = L.marker([lat,lng]).addTo(custMap); } else { custMarker.setLatLng([lat,lng]); }
            custMap.setView([lat,lng], 15);
        }, 50);
    });
    </script>
    @endpush
</div>
@endsection


