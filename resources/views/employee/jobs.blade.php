@extends('layouts.employee')

@section('title','My Jobs')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold">My Jobs</h1>

    <div class="mt-6 overflow-auto">
        <table class="min-w-full bg-white rounded border text-sm">
            <thead class="bg-emerald-50">
                <tr class="text-left font-semibold">
                    <th class="p-2">Booking ID</th>
                    <th class="p-2">Date & Time</th>
                    <th class="p-2">Customer Name</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr class="border-t">
                    <td class="p-2">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</td>
                    <td class="p-2">{{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('m/d/y g:i A') : '—' }}</td>
                    <td class="p-2">{{ $b->customer_name ?? '—' }}</td>
                    <td class="p-2 capitalize">{{ $b->status }}</td>
                    <td class="p-2">
                        <div class="flex items-center gap-2">
                            @if($b->status === 'in_progress')
                                <form method="POST" action="{{ route('employee.jobs.complete', $b->id) }}">
                                    @csrf
                                    <button class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" title="Mark as complete">
                                        <span class="sr-only">Complete</span>
                                        <i class="ri-check-line"></i>
                                    </button>
                                </form>
                            @elseif($b->status === 'pending' || $b->status === 'confirmed')
                                <form method="POST" action="{{ route('employee.jobs.start', $b->id) }}">
                                    @csrf
                                    <button class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" title="Start Job">
                                        <span class="sr-only">Start</span>
                                        <i class="ri-play-line"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="openEmpReceipt({{ $b->id }})" title="View Receipt">
                                <span class="sr-only">View Receipt</span>
                                <i class="ri-receipt-line"></i>
                            </button>
                            <button type="button" class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="openEmpLocation({ id: {{ $b->id }}, lat: {{ $b->latitude ?? 0 }}, lng: {{ $b->longitude ?? 0 }} })" title="View Location">
                                <span class="sr-only">View Location</span>
                                <i class="ri-map-pin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $bookings->links() }}</div>
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