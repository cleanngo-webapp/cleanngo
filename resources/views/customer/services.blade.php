@extends('layouts.app')

@section('title','Request a Booking')

@section('content')
<div class="max-w-6xl mx-auto pt-20">
    <h1 class="text-3xl font-extrabold text-center">Request a Booking</h1>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Services Nav -->
        <aside class="bg-emerald-700 flex flex-col rounded-xl border-none p-3 gap-2">
            <div class="font-semibold flex flex-col mb-2 text-center text-white">SERVICES</div>
            <div class="flex flex-col gap-2">
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="sofa">Sofa / Mattress Deep Cleaning</button>
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="carpet">Carpet Deep Cleaning</button>
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="carInterior">Home Service Car Interior Detailing</button>
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="postConstruction">Post Construction Cleaning</button>
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="disinfection">Enhanced Disinfection</button>
                <button class="text-left text-white px-3 py-2 rounded hover:bg-gray-100 hover:text-black cursor-pointer" data-service="glass">Glass Cleaning</button>
            </div>
        </aside>

        <!-- Middle: Active Service Form -->
        <section class="bg-brand-green rounded-xl text-white p-4 md:col-span-1" id="serviceForms">
            <!-- Sofa/Mattress -->
            <div data-form="sofa" class="hidden">
                <h2 class="font-semibold text-center">Sofa Deep Cleaning</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">1 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_1" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">2 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_2" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">3 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_3" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">4 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_4" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">5 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_5" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">6 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_6" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">7 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_7" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">8 seater</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_8" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">L-shape</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_l" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Cross Sectional</span><span class="w-10 text-center text-white">Qty</span><input id="sofa_cross" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                </div>
                <h2 class="font-semibold mt-6 text-center">Mattress Deep Cleaning</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">Single bed</span><span class="w-10 text-center text-white">Qty</span><input id="mattress_single" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Double bed</span><span class="w-10 text-center text-white">Qty</span><input id="mattress_double" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">King bed</span><span class="w-10 text-center text-white">Qty</span><input id="mattress_king" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">California bed</span><span class="w-10 text-center text-white">Qty</span><input id="mattress_california" type="number" min="0" value="0" class="border rounded px-1 py-1 w-20 text-right"></div>
            </div>
            </div>

            <!-- Carpet -->
            <div data-form="carpet" class="hidden">
                <h2 class="font-semibold text-center">Carpet Deep Cleaning</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">How many square meters?</span><span class="w-10 text-center text-white">Qty</span><input id="carpet_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Quantity</span><span class="w-10 text-center text-white">Qty</span><input id="carpet_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                </div>
            </div>

            <!-- Car Interior -->
            <div data-form="carInterior" class="hidden">
                <h2 class="font-semibold text-center">Home Service Car Interior Detailing</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">Sedan</span><span class="w-10 text-center text-white">Qty</span><input id="car_sedan" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">SUV</span><span class="w-10 text-center text-white">Qty</span><input id="car_suv" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Van</span><span class="w-10 text-center text-white">Qty</span><input id="car_van" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Coaster</span><span class="w-10 text-center text-white">Qty</span><input id="car_coaster" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                </div>
            </div>

            <!-- Post Construction -->
            <div data-form="postConstruction" class="hidden">
                <h2 class="font-semibold text-center">Post Construction Cleaning</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">How many square meters?</span><span class="w-10 text-center text-white">Qty</span><input id="pcc_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Quantity</span><span class="w-10 text-center text-white">Qty</span><input id="pcc_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                </div>
            </div>

            <!-- Enhanced Disinfection -->
            <div data-form="disinfection" class="hidden">
                <h2 class="font-semibold text-center">Enhanced Disinfection</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">How many square meters?</span><span class="w-10 text-center text-white">Qty</span><input id="disinfect_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Quantity</span><span class="w-10 text-center text-white">Qty</span><input id="disinfect_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                </div>
            </div>

            <!-- Glass -->
            <div data-form="glass" class="hidden">
                <h2 class="font-semibold text-center">Glass Cleaning</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <div class="flex items-center gap-2"><span class="flex-1">How many square meters?</span><span class="w-10 text-center text-white">Qty</span><input id="glass_sqm" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                    <div class="flex items-center gap-2"><span class="flex-1">Quantity</span><span class="w-10 text-center text-white">Qty</span><input id="glass_qty" type="number" min="0" value="0" class="border rounded px-2 py-1 w-20 text-right"></div>
                </div>
            </div>
        </section>

        <!-- Right: Receipt -->
        <aside class="bg-white rounded-xl shadow-sm p-4">
            <div class="font-bold text-lg uppercase mb-2">Total Estimation</div>
            <div class="text-xs text-gray-500 mb-3" id="receipt_title">Sofa / Mattress Deep Cleaning</div>
            
            <!-- Service Items List -->
            <div id="receipt_lines" class="space-y-3 mb-4"></div>
            
            <!-- Separator Line -->
            <div class="border-t border-gray-300 my-3"></div>
            
            <!-- Totals -->
            <div class="space-y-1">
                <div class="text-sm flex justify-between">
                    <span>Subtotal</span> 
                    <span id="estimate_subtotal">PHP 0.00</span>
                </div>
                <div class="text-sm flex justify-between font-bold">
                    <span>TOTAL</span> 
                    <span id="estimate_total">PHP 0.00</span>
                </div>
            </div>
            
            <!-- Book Now Button -->
            <button class="mt-4 w-full px-4 py-2 bg-emerald-600 text-white rounded cursor-pointer hover:bg-emerald-700 transition-colors duration-200" onclick="openBookingForm()">Book Now</button>
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
  // Highlight active left nav item
  navButtons.forEach(btn => {
    const isActive = btn.dataset.service === name;
    btn.classList.toggle('bg-white', isActive);
    btn.classList.toggle('text-black', isActive);
    btn.classList.toggle('text-white', !isActive);
  });
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
document.addEventListener('DOMContentLoaded', () => {
  // Default
  let initial = 'sofa';
  const hash = (window.location.hash||'').replace('#','');
  const valid = ['sofa','carpet','carInterior','postConstruction','disinfection','glass'];
  if (valid.includes(hash)) initial = hash;
  showForm(initial);
});

function calc(){
  const receipt = [];
  let subtotal = 0;
  let itemCounter = 1;

  const u = id => parseInt(document.getElementById(id)?.value || 0);
  const s = id => parseFloat(document.getElementById(id)?.value || 0);

  // Group Sofa and Mattress services together
  const sofaItems = [];
  const sofaInputs = [
    {id: 'sofa_1', label: '1 seater', qty: u('sofa_1')},
    {id: 'sofa_2', label: '2 seater', qty: u('sofa_2')},
    {id: 'sofa_3', label: '3 seater', qty: u('sofa_3')},
    {id: 'sofa_4', label: '4 seater', qty: u('sofa_4')},
    {id: 'sofa_5', label: '5 seater', qty: u('sofa_5')},
    {id: 'sofa_6', label: '6 seater', qty: u('sofa_6')},
    {id: 'sofa_7', label: '7 seater', qty: u('sofa_7')},
    {id: 'sofa_8', label: '8 seater', qty: u('sofa_8')},
    {id: 'sofa_l', label: 'L-shape', qty: u('sofa_l')},
    {id: 'sofa_cross', label: 'Cross Sectional', qty: u('sofa_cross')}
  ];

  const mattressItems = [];
  const mattressInputs = [
    {id: 'mattress_single', label: 'Single', qty: u('mattress_single')},
    {id: 'mattress_double', label: 'Double', qty: u('mattress_double')},
    {id: 'mattress_king', label: 'King', qty: u('mattress_king')},
    {id: 'mattress_california', label: 'California', qty: u('mattress_california')}
  ];

  // Collect sofa items
  sofaInputs.forEach(item => {
    if (item.qty > 0) {
      sofaItems.push({...item, price: 4000, total: item.qty * 4000});
    }
  });

  // Collect mattress items
  mattressInputs.forEach(item => {
    if (item.qty > 0) {
      mattressItems.push({...item, price: 4000, total: item.qty * 4000});
    }
  });

  // Create combined Sofa/Mattress card if any items exist
  if (sofaItems.length > 0 || mattressItems.length > 0) {
    const allItems = [...sofaItems, ...mattressItems];
    const totalAmount = allItems.reduce((sum, item) => sum + item.total, 0);
    
    let itemsHtml = '';
    allItems.forEach(item => {
      itemsHtml += `
        <div class="flex justify-between items-center py-1">
          <div class="flex-1">
            <span class="text-xs text-gray-600">${item.label}</span>
            <span class="text-xs text-gray-500 ml-2">x ${item.qty}</span>
          </div>
          <span class="text-xs font-semibold">${peso(item.total)}</span>
        </div>
      `;
    });

    receipt.push(`
      <div class="border border-gray-200 rounded-lg p-3">
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <div class="font-semibold text-sm">${itemCounter}. Sofa / Mattress Deep Cleaning</div>
            <div class="mt-2 space-y-1">
              ${itemsHtml}
            </div>
          </div>
          <button onclick="removeSofaMattressGroup()" class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 cursor-pointer">Remove</button>
        </div>
        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
          <span class="text-xs text-gray-500">Total</span>
          <span class="font-semibold">${peso(totalAmount)}</span>
        </div>
      </div>
    `);
    subtotal += totalAmount;
    itemCounter++;
  }

  // Car Interior Detailing - group by type
  const carItems = [];
  const carInputs = [
    {id: 'car_sedan', label: 'Sedan', qty: u('car_sedan')},
    {id: 'car_suv', label: 'SUV', qty: u('car_suv')},
    {id: 'car_van', label: 'Van', qty: u('car_van')},
    {id: 'car_coaster', label: 'Coaster', qty: u('car_coaster')}
  ];

  carInputs.forEach(item => {
    if (item.qty > 0) {
      carItems.push({...item, price: 4000, total: item.qty * 4000});
    }
  });

  if (carItems.length > 0) {
    const totalAmount = carItems.reduce((sum, item) => sum + item.total, 0);
    
    let itemsHtml = '';
    carItems.forEach(item => {
      itemsHtml += `
        <div class="flex justify-between items-center py-1">
          <div class="flex-1">
            <span class="text-xs text-gray-600">${item.label}</span>
            <span class="text-xs text-gray-500 ml-2">x ${item.qty}</span>
          </div>
          <span class="text-xs font-semibold">${peso(item.total)}</span>
        </div>
      `;
    });

    receipt.push(`
      <div class="border border-gray-200 rounded-lg p-3">
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <div class="font-semibold text-sm">${itemCounter}. Car Interior Detailing</div>
            <div class="mt-2 space-y-1">
              ${itemsHtml}
            </div>
          </div>
          <button onclick="removeCarGroup()" class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 cursor-pointer">Remove</button>
        </div>
        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
          <span class="text-xs text-gray-500">Total</span>
          <span class="font-semibold">${peso(totalAmount)}</span>
        </div>
      </div>
    `);
    subtotal += totalAmount;
    itemCounter++;
  }

  // SQM-based services (each as separate card)
  const sqmServices = [
    {label: 'Carpet Deep Cleaning', sqmId: 'carpet_sqm', qtyId: 'carpet_qty'},
    {label: 'Post Construction Cleaning', sqmId: 'pcc_sqm', qtyId: 'pcc_qty'},
    {label: 'Enhanced Disinfection', sqmId: 'disinfect_sqm', qtyId: 'disinfect_qty'},
    {label: 'Glass Cleaning', sqmId: 'glass_sqm', qtyId: 'glass_qty'}
  ];

  sqmServices.forEach(service => {
    const sqm = s(service.sqmId), qty = s(service.qtyId);
    if (sqm > 0 && qty > 0) {
      const amt = sqm * qty * 500;
      receipt.push(`
        <div class="border border-gray-200 rounded-lg p-3">
          <div class="flex items-start justify-between mb-2">
            <div class="flex-1">
              <div class="font-semibold text-sm">${itemCounter}. ${service.label}</div>
              <div class="text-xs text-gray-600">How many square meters?</div>
              <div class="text-xs text-gray-500 text-right">${sqm}</div>
              <div class="text-xs text-gray-600">Qty</div>
              <div class="text-xs text-gray-500 text-right">${qty}</div>
            </div>
            <button onclick="removeSqmItem('${service.sqmId}', '${service.qtyId}')" class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 cursor-pointer">Remove</button>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">x ${qty}</span>
            <span class="font-semibold">${peso(amt)}</span>
          </div>
        </div>
      `);
      subtotal += amt;
      itemCounter++;
    }
  });

  document.getElementById('receipt_lines').innerHTML = receipt.join('');
  document.getElementById('estimate_subtotal').textContent = peso(subtotal);
  document.getElementById('estimate_total').textContent = peso(subtotal);
  return subtotal;
}
document.addEventListener('input', function(e){ if(e.target.closest('input')) calc(); });

// Remove item functions
function removeItem(inputId) {
  document.getElementById(inputId).value = 0;
  calc();
}

function removeSqmItem(sqmId, qtyId) {
  document.getElementById(sqmId).value = 0;
  document.getElementById(qtyId).value = 0;
  calc();
}

function removeSofaMattressGroup() {
  // Clear all sofa inputs
  ['sofa_1', 'sofa_2', 'sofa_3', 'sofa_4', 'sofa_5', 'sofa_6', 'sofa_7', 'sofa_8', 'sofa_l', 'sofa_cross'].forEach(id => {
    document.getElementById(id).value = 0;
  });
  // Clear all mattress inputs
  ['mattress_single', 'mattress_double', 'mattress_king', 'mattress_california'].forEach(id => {
    document.getElementById(id).value = 0;
  });
  calc();
}

function removeCarGroup() {
  // Clear all car inputs
  ['car_sedan', 'car_suv', 'car_van', 'car_coaster'].forEach(id => {
    document.getElementById(id).value = 0;
  });
  calc();
}

function openBookingForm(){
  const total = calc();
  
  // Check if any services are selected
  if (total <= 0) {
    Swal.fire({
      title: 'No Services Selected',
      text: 'Please select at least one service before booking.',
      icon: 'warning',
      confirmButtonText: 'OK',
      confirmButtonColor: '#10b981'
    });
    return;
  }
  
  // Build items payload to persist line items
  const items = [];
  const addItem = (type, qty, unitPrice, areaSqm) => {
    qty = parseInt(qty||0); if(!qty && !areaSqm) return; items.push({ type, qty, unitPrice, areaSqm }); };
  // Sofa/Mattress
  addItem('sofa_1_seater', document.getElementById('sofa_1').value, 4000);
  addItem('sofa_2_seater', document.getElementById('sofa_2').value, 4000);
  addItem('sofa_3_seater', document.getElementById('sofa_3').value, 4000);
  addItem('sofa_4_seater', document.getElementById('sofa_4').value, 4000);
  addItem('sofa_5_seater', document.getElementById('sofa_5').value, 4000);
  addItem('sofa_6_seater', document.getElementById('sofa_6').value, 4000);
  addItem('sofa_7_seater', document.getElementById('sofa_7').value, 4000);
  addItem('sofa_8_seater', document.getElementById('sofa_8').value, 4000);
  addItem('sofa_l_shape', document.getElementById('sofa_l').value, 4000);
  addItem('sofa_cross', document.getElementById('sofa_cross').value, 4000);
  addItem('mattress_single', document.getElementById('mattress_single').value, 4000);
  addItem('mattress_double', document.getElementById('mattress_double').value, 4000);
  addItem('mattress_king', document.getElementById('mattress_king').value, 4000);
  addItem('mattress_california', document.getElementById('mattress_california').value, 4000);
  // Carpet/Post/Disinfect/Glass (sqm * qty * 500)
  const addSqm = (label, sqmId, qtyId) => {
    const sqm = parseFloat(document.getElementById(sqmId)?.value||0);
    const qty = parseInt(document.getElementById(qtyId)?.value||0);
    if (sqm>0 && qty>0) items.push({ type: label, qty, unitPrice: 500, areaSqm: sqm });
  };
  addSqm('carpet_sqm', 'carpet_sqm', 'carpet_qty');
  addSqm('post_construction_sqm', 'pcc_sqm', 'pcc_qty');
  addSqm('disinfect_sqm', 'disinfect_sqm', 'disinfect_qty');
  addSqm('glass_sqm', 'glass_sqm', 'glass_qty');
  // Car detailing
  addItem('car_sedan', document.getElementById('car_sedan').value, 4000);
  addItem('car_suv', document.getElementById('car_suv').value, 4000);
  addItem('car_van', document.getElementById('car_van').value, 4000);
  addItem('car_coaster', document.getElementById('car_coaster').value, 4000);

  window.dispatchEvent(new CustomEvent('openBookingModal', {detail: {total, items}}));
}
</script>
<div id="booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000] overflow-y-auto">
  <div class="bg-white rounded-xl w-full max-w-lg p-4 m-4">
    <div class="flex items-center justify-between mb-4">
      <div class="font-semibold text-lg">Confirm Booking</div>
      <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeBookingModal()">✕</button>
    </div>
    
    <form method="POST" action="{{ route('customer.bookings.create') }}" class="space-y-4" onsubmit="return confirmBookingSubmission(event)">
      @csrf
      
      <!-- Customer Information Section -->
      <div class="space-y-2">
        <div class="text-sm">
          <span class="text-gray-600">Name:</span> 
          <span class="font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
        </div>
        <div class="text-sm">
          <span class="text-gray-600">Contact:</span> 
          <span class="font-medium">{{ auth()->user()->phone ?? '—' }}</span>
        </div>
        
        @php $primary = optional(DB::table('addresses')->where('user_id', auth()->id())->orderByDesc('is_primary')->orderBy('id')->first()); @endphp
        @if(!$primary)
          <div class="text-red-600 text-sm bg-red-50 p-2 rounded border border-red-200">
            Please set your address first before booking.
          </div>
        @else
          <div class="text-sm">
            <span class="text-gray-600">Address:</span> 
            <span class="font-medium">{{ $primary->line1 }} {{ $primary->city ? ', '.$primary->city : '' }} {{ $primary->province ? ', '.$primary->province : '' }}</span>
          </div>
          <input type="hidden" name="address_id" value="{{ $primary->id }}">
        @endif
      </div>
      
      <!-- Date and Time Selection -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
          <input required type="date" name="date" class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
          <input required type="time" name="time" class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
      </div>
      
      <!-- Hidden fields for booking data -->
      <input type="hidden" name="total" id="booking_total">
      <input type="hidden" name="items_json" id="items_json">
      
      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded cursor-pointer hover:bg-gray-600 transition-colors duration-200" onclick="closeBookingModal()">
          Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-800 transition-colors duration-200" @if(!$primary) disabled @endif>
          Book Now
        </button>
      </div>
    </form>
  </div>
  <script>
  // Function to close the booking modal
  function closeBookingModal() {
    const modal = document.getElementById('booking-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  // Event listener for opening the booking modal
  window.addEventListener('openBookingModal', function(e){
    const modal = document.getElementById('booking-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('booking_total').value = e.detail.total;
    document.getElementById('items_json').value = JSON.stringify(e.detail.items||[]);
  });

  // Close modal when clicking outside the modal content
  document.getElementById('booking-modal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeBookingModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeBookingModal();
    }
  });

  // Function to handle form submission confirmation
  function confirmBookingSubmission(event) {
    event.preventDefault();
    
    Swal.fire({
      title: 'Confirm Booking',
      text: 'Are you sure you want to submit this booking?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Book Now!',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#ef4444'
    }).then((result) => {
      if (result.isConfirmed) {
        // Submit the form
        event.target.submit();
      }
    });
    
    return false; // Prevent default form submission
  }
  </script>
</div>
@endpush

