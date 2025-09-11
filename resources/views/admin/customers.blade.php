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

    <div class="mt-6 overflow-auto">
        <table class="min-w-full bg-white rounded border text-sm">
            <thead class="bg-emerald-50">
                <tr class="text-left font-semibold border-b">
                    <th class="p-2 text-center text-2xl" colspan="8">Customer Records Table</th>
                </tr>
                <tr class="text-left font-semibold">
                    <th class="p-2">Customer ID</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Contact</th>
                    <th class="p-2">Total Bookings</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $cust)
                    <tr class="border-t">
                        <td class="p-2">{{ $cust->customer_code ?? ($cust->customer_id ? sprintf('C%04d%03d', date('Y'), $cust->customer_id % 1000) : '—') }}</td>
                        <td class="p-2">{{ trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '')) ?: $cust->username }}</td>
                        <td class="p-2">{{ $cust->phone ?? '—' }}</td>
                        <td class="p-2">{{ $cust->bookings_count ?? 0 }}</td>
                        <td class="p-2">
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="window.dispatchEvent(new CustomEvent('showCustomerMap',{detail:{userId:{{ $cust->user_id }}}}))" title="View Location">
                                    <span class="sr-only">View Location</span>
                                    <i class="ri-map-pin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @isset($customers)
            <div class="mt-2">{{ $customers->links() }}</div>
        @endisset
    </div>
    <div id="customer-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Customer Location</div>
                <button class="cursor-pointer" onclick="hideCustMap()">✕</button>
            </div>
            <div id="custLocationAddress" class="text-sm mb-1 text-gray-700"></div>
            <div id="custLocationPhone" class="text-xs mb-2 text-gray-500"></div>
            <div id="customerMap" class="h-80 rounded border"></div>
        </div>
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    var custMap, custMarker;
    function hideCustMap(){
        const m = document.getElementById('customer-map-modal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }
    window.addEventListener('showCustomerMap', async function(e){
        const userId = e.detail.userId;
        // fetch primary address for the customer
        const res = await fetch('/api/user/'+userId+'/primary-address');
        const data = await res.json();
        const modalEl = document.getElementById('customer-map-modal');
        modalEl.classList.remove('hidden');
        modalEl.classList.add('flex');
        const addrEl = document.getElementById('custLocationAddress');
        const phoneEl = document.getElementById('custLocationPhone');
        const composed = [data?.line1, data?.barangay, data?.city, data?.province].filter(Boolean).join(', ');
        addrEl.textContent = composed || 'No address available';
        phoneEl.textContent = data?.phone ? ('Contact: ' + data.phone) : '';
        setTimeout(function(){
            if(!custMap){
                custMap = L.map('customerMap').setView([14.5995,120.9842], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(custMap);
            }
            var lat = data?.latitude ?? 14.5995, lng = data?.longitude ?? 120.9842;
            if(!custMarker){ custMarker = L.marker([lat,lng]).addTo(custMap); } else { custMarker.setLatLng([lat,lng]); }
            custMap.setView([lat,lng], 15);
            setTimeout(function(){ if (custMap) custMap.invalidateSize(true); }, 100);
        }, 50);
    });
    </script>
    @endpush
</div>
@endsection


