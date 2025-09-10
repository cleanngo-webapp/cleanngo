@extends('layouts.app')

@section('title','Request an Estimate')

@section('content')
<div class="max-w-6xl mx-auto pt-20">
    <h1 class="text-3xl font-extrabold text-center">Request an Estimate</h1>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Services Nav -->
        <aside class="bg-white rounded-xl border p-3">
            <div class="font-semibold text-center mb-2">Services</div>
            <div class="flex flex-col">
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="sofa">Sofa/ Mattress Deep Cleaning</button>
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="carpet">Carpet Deep Cleaning</button>
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="carInterior">Home Service Car Interior Detailing</button>
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="postConstruction">Post Construction Cleaning</button>
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="disinfection">Enhanced Disinfection</button>
                <button class="text-left px-3 py-2 rounded hover:bg-gray-100" data-service="glass">Glass Cleaning</button>
            </div>
        </aside>

        <!-- Middle: Active Service Form -->
        <section class="bg-white rounded-xl border p-4 md:col-span-1" id="serviceForms">
            <!-- Sofa/Mattress -->
            <div data-form="sofa" class="hidden">
                <h2 class="font-semibold text-center">Sofa Deep Cleaning</h2>
                <div class="mt-3 grid grid-cols-3 gap-2 text-sm">
                    <label>1 seater <input id="sofa_1" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>2 seater <input id="sofa_2" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>3 seater <input id="sofa_3" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>4 seater <input id="sofa_4" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>5 seater <input id="sofa_5" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>6 seater <input id="sofa_6" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>7 seater <input id="sofa_7" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>8 seater <input id="sofa_8" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>L-shape <input id="sofa_l" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>Cross Sectional <input id="sofa_cross" type="number" min="0" value="0" class="border rounded px-2 py-1 w-24 ml-2"></label>
                </div>
                <h2 class="font-semibold text-center mt-6">Mattress Deep Cleaning</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>Single bed <input id="mattress_single" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>Double bed <input id="mattress_double" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>King bed <input id="mattress_king" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>California bed <input id="mattress_california" type="number" min="0" value="0" class="border rounded px-2 py-1 w-24 ml-2"></label>
                </div>
            </div>

            <!-- Carpet -->
            <div data-form="carpet" class="hidden">
                <h2 class="font-semibold text-center">Carpet Deep Cleaning</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>How many square meters? <input id="carpet_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                    <label>Quantity <input id="carpet_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                </div>
            </div>

            <!-- Car Interior -->
            <div data-form="carInterior" class="hidden">
                <h2 class="font-semibold text-center">Home Service Car Interior Detailing</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>Sedan <input id="car_sedan" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>SUV <input id="car_suv" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>Van <input id="car_van" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 ml-2"></label>
                    <label>Coaster <input id="car_coaster" type="number" min="0" value="0" class="border rounded px-2 py-1 w-24 ml-2"></label>
                </div>
            </div>

            <!-- Post Construction -->
            <div data-form="postConstruction" class="hidden">
                <h2 class="font-semibold text-center">Post Construction Cleaning</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>How many square meters? <input id="pcc_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                    <label>Quantity <input id="pcc_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                </div>
            </div>

            <!-- Enhanced Disinfection -->
            <div data-form="disinfection" class="hidden">
                <h2 class="font-semibold text-center">Enhanced Disinfection</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>How many square meters? <input id="disinfect_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                    <label>Quantity <input id="disinfect_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                </div>
            </div>

            <!-- Glass -->
            <div data-form="glass" class="hidden">
                <h2 class="font-semibold text-center">Glass Cleaning</h2>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <label>How many square meters? <input id="glass_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                    <label>Quantity <input id="glass_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-full ml-2"></label>
                </div>
            </div>
        </section>

        <!-- Right: Receipt -->
        <aside class="bg-white rounded-xl border p-4">
            <div class="font-semibold">Total Estimation</div>
            <div class="text-xs text-gray-500" id="receipt_title">Sofa / Mattress Deep Cleaning</div>
            <div class="mt-2 text-sm" id="receipt_lines"></div>
            <div class="mt-2 text-sm flex justify-between"><span>Subtotal</span> <span id="estimate_subtotal">PHP 0.00</span></div>
            <div class="text-sm flex justify-between font-semibold"><span>TOTAL</span> <span id="estimate_total">PHP 0.00</span></div>
            <button class="mt-3 px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="openBookingForm()">Book Now</button>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
const peso = v => 'PHP ' + Number(v||0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

// Service switching
const forms = document.querySelectorAll('#serviceForms [data-form]');
const navButtons = document.querySelectorAll('[data-service]');
function showForm(name){
  forms.forEach(f => f.classList.toggle('hidden', f.getAttribute('data-form') !== name));
  const titles = {
    sofa: 'Sofa / Mattress Deep Cleaning',
    carpet: 'Carpet Deep Cleaning',
    carInterior: 'Home Service Car Interior Detailing',
    postConstruction: 'Post Construction Cleaning',
    disinfection: 'Enhanced Disinfection',
    glass: 'Glass Cleaning'
  };
  document.getElementById('receipt_title').textContent = titles[name];
  calc();
}
navButtons.forEach(btn => btn.addEventListener('click', () => showForm(btn.dataset.service)));
document.addEventListener('DOMContentLoaded', () => showForm('sofa'));

function calc(){
  const receipt = [];
  let subtotal = 0;

  // 4000 per unit items
  const u = id => parseInt(document.getElementById(id)?.value || 0);
  const addLine = (label, qty, price) => { if(qty>0){ receipt.push(`<div class=\"flex justify-between\"><span>${label} x ${qty}</span><span>${peso(qty*price)}</span></div>`); subtotal += qty*price; } };

  addLine('Sofa 1-seater', u('sofa_1'), 4000);
  addLine('Sofa 2-seater', u('sofa_2'), 4000);
  addLine('Sofa 3-seater', u('sofa_3'), 4000);
  addLine('Sofa 4-seater', u('sofa_4'), 4000);
  addLine('Sofa 5-seater', u('sofa_5'), 4000);
  addLine('Sofa 6-seater', u('sofa_6'), 4000);
  addLine('Sofa 7-seater', u('sofa_7'), 4000);
  addLine('Sofa 8-seater', u('sofa_8'), 4000);
  addLine('Sofa L-shape', u('sofa_l'), 4000);
  addLine('Sofa Cross Sectional', u('sofa_cross'), 4000);

  addLine('Mattress Single', u('mattress_single'), 4000);
  addLine('Mattress Double', u('mattress_double'), 4000);
  addLine('Mattress King', u('mattress_king'), 4000);
  addLine('Mattress California', u('mattress_california'), 4000);

  // sqm * qty * 500 services
  const s = id => parseFloat(document.getElementById(id)?.value || 0);
  const sqmLine = (label, sqmId, qtyId) => {
    const sqm = s(sqmId), qty = s(qtyId);
    if (sqm>0 && qty>0) {
      const amt = sqm * qty * 500;
      receipt.push(`<div class=\"flex justify-between\"><span>${label}: ${sqm} sqm x ${qty}</span><span>${peso(amt)}</span></div>`);
      subtotal += amt;
    }
  };
  sqmLine('Carpet Deep Cleaning', 'carpet_sqm', 'carpet_qty');
  sqmLine('Post Construction Cleaning', 'pcc_sqm', 'pcc_qty');
  sqmLine('Enhanced Disinfection', 'disinfect_sqm', 'disinfect_qty');
  sqmLine('Glass Cleaning', 'glass_sqm', 'glass_qty');

  // Car detailing (4000 per qty by type)
  addLine('Car Detailing - Sedan', u('car_sedan'), 4000);
  addLine('Car Detailing - SUV', u('car_suv'), 4000);
  addLine('Car Detailing - Van', u('car_van'), 4000);
  addLine('Car Detailing - Coaster', u('car_coaster'), 4000);

  document.getElementById('receipt_lines').innerHTML = receipt.join('');
  document.getElementById('estimate_subtotal').textContent = peso(subtotal);
  document.getElementById('estimate_total').textContent = peso(subtotal);
  return subtotal;
}
document.addEventListener('input', function(e){ if(e.target.closest('input')) calc(); });

function openBookingForm(){
  const total = calc();
  window.dispatchEvent(new CustomEvent('openBookingModal', {detail: {total}}));
}
</script>
<div id="booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
  <div class="bg-white rounded-xl w-full max-w-lg p-4">
    <div class="flex items-center justify-between mb-2">
      <div class="font-semibold">Confirm Booking</div>
      <button onclick="const modal = document.getElementById('booking-modal'); modal.classList.add('hidden'); modal.classList.remove('flex');">✕</button>
    </div>
    <form method="POST" action="{{ route('customer.bookings.create') }}" class="space-y-2">
      @csrf
      <div>Name: <span class="font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span></div>
      <div>Contact: <span class="font-medium">{{ auth()->user()->phone ?? '—' }}</span></div>
      @php $primary = optional(DB::table('addresses')->where('user_id', auth()->id())->orderByDesc('is_primary')->orderBy('id')->first()); @endphp
      @if(!$primary)
        <div class="text-red-600">Please set your address first before booking.</div>
      @else
        <div>Address: <span class="font-medium">{{ $primary->line1 }} {{ $primary->city ? ', '.$primary->city : '' }} {{ $primary->province ? ', '.$primary->province : '' }}</span></div>
        <input type="hidden" name="address_id" value="{{ $primary->id }}">
      @endif
      <div class="grid grid-cols-2 gap-2">
        <label class="text-sm">Date <input required type="date" name="date" class="border rounded px-2 py-1 w-full"></label>
        <label class="text-sm">Time <input required type="time" name="time" class="border rounded px-2 py-1 w-full"></label>
      </div>
      <input type="hidden" name="total" id="booking_total">
      <div class="flex justify-end gap-2 mt-2">
        <button type="submit" class="px-3 py-2 bg-emerald-700 text-white rounded" @if(!$primary) disabled @endif>Book Now</button>
      </div>
    </form>
  </div>
  <script>
  window.addEventListener('openBookingModal', function(e){
    const modal = document.getElementById('booking-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('booking_total').value = e.detail.total;
  });
  </script>
</div>
@endpush

