@extends('layouts.employee')

@section('title','My Jobs')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">My Jobs</h1>

    {{-- My Jobs Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">My Job Assignments</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage your assigned jobs and track progress</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y g:i A') : '—' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $b->customer_name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'in_progress' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'no_show' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$b->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($b->status === 'in_progress')
                                    <form method="POST" action="{{ route('employee.jobs.complete', $b->id) }}" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors cursor-pointer" title="Mark as complete">
                                            <i class="ri-check-line mr-1"></i>
                                            Complete
                                        </button>
                                    </form>
                                @elseif($b->status === 'pending' || $b->status === 'confirmed')
                                    <form method="POST" action="{{ route('employee.jobs.start', $b->id) }}" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors cursor-pointer" title="Start Job">
                                            <i class="ri-play-line mr-1"></i>
                                            Start Job
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpReceipt({{ $b->id }})" title="View Receipt">
                                    <i class="ri-receipt-line mr-1"></i>
                                    Receipt
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpLocation({ id: {{ $b->id }}, lat: {{ $b->latitude ?? 0 }}, lng: {{ $b->longitude ?? 0 }} })" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    Location
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    </div>
    <div id="job-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Customer Location</div>
                <button class="cursor-pointer" onclick="const m=document.getElementById('job-map-modal'); m.classList.add('hidden'); m.classList.remove('flex');">✕</button>
            </div>
            <div id="empLocationAddress" class="text-sm mb-1 text-gray-700"></div>
            <div id="empLocationPhone" class="text-xs mb-2 text-gray-500"></div>
            <div id="jobMap" class="h-80 rounded border"></div>
        </div>
    </div>
    <div id="emp-receipt-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Service Receipt</div>
                <button class="cursor-pointer" onclick="closeEmpReceipt()">✕</button>
            </div>
            <div id="empReceiptBody" class="text-sm space-y-1"></div>
            <div class="mt-4 flex justify-end">
                <button class="bg-emerald-700 text-white px-3 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeEmpReceipt()">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let jobMap, jobMarker;
function openEmpLocation(payload){
    const lat = payload?.lat ?? 0, lng = payload?.lng ?? 0;
    const modal = document.getElementById('job-map-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const addr = document.getElementById('empLocationAddress');
    const phone = document.getElementById('empLocationPhone');
    // find address and phone from server-provided locationsData if available
    try {
        const data = (window.empLocations && (window.empLocations[payload.id] || window.empLocations[String(payload.id)])) || null;
        if (data) {
            addr.textContent = data.address || '';
            phone.textContent = data.phone ? ('Contact: ' + data.phone) : '';
        } else {
            addr.textContent = '';
            phone.textContent = '';
        }
    } catch(e){ addr.textContent=''; phone.textContent=''; }
    setTimeout(function(){
        if(!jobMap){
            jobMap = L.map('jobMap').setView([lat,lng], (lat&&lng)?15:5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(jobMap);
        } else { jobMap.setView([lat,lng], (lat&&lng)?15:5); }
        if(!jobMarker){ jobMarker = L.marker([lat,lng]).addTo(jobMap); } else { jobMarker.setLatLng([lat,lng]); }
        setTimeout(()=>{ if(jobMap) jobMap.invalidateSize(true); }, 100);
    }, 50);
}
// Make locations available globally for address/phone rendering
window.empLocations = @json($locationsData ?? []);
const empReceipts = @json($receiptData ?? []);
function peso(v){ return 'PHP ' + Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function openEmpReceipt(id){
    const data = empReceipts[String(id)] || empReceipts[id];
    const modal = document.getElementById('emp-receipt-modal');
    const body = document.getElementById('empReceiptBody');
    if(!data){
        body.innerHTML = '<div class="text-sm text-gray-500">No items recorded for this booking.</div>';
    } else {
        const lines = (data.lines||[]).map(l=>`<div class=\"flex justify-between\"><span>${l.label}</span><span>${peso(l.amount)}</span></div>`).join('');
        body.innerHTML = lines + `<div class=\"mt-2 flex justify-between font-semibold\"><span>Total</span><span>${peso(data.total||0)}</span></div>`;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeEmpReceipt(){
    const modal = document.getElementById('emp-receipt-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush