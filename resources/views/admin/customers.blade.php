@extends('layouts.admin')

@section('title','Customers')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Customers</h1>

    {{-- Search and Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Search customers..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                        <i class="ri-calendar-line mr-2"></i>
                        Filter by Date
                    </button>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                        <i class="ri-service-line mr-2"></i>
                        Filter by Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Records Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Customer Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage customer information and booking history</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($customers as $cust)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $cust->customer_code ?? ($cust->customer_id ? sprintf('C%04d%03d', date('Y'), $cust->customer_id % 1000) : '—') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '')) ?: $cust->username }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cust->phone ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($cust->bookings_count ?? 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('showCustomerMap',{detail:{userId:{{ $cust->user_id }}}}))" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    View Location
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No customers found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @isset($customers)
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
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


