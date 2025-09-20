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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
<div id="booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]" style="overscroll-behavior: contain;">
  <div class="bg-white rounded-xl w-full max-w-lg p-4 m-4" style="max-height: 90vh; overflow-y: auto; overscroll-behavior: contain;">
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
        
        @php 
          // Use the Address model to ensure proper casting of coordinates
          $primary = \App\Models\Address::where('user_id', auth()->id())->orderByDesc('is_primary')->orderBy('id')->first();
          // Make this variable available globally for JavaScript
          $GLOBALS['primaryAddress'] = $primary;
        @endphp
        @if(!$primary)
          <div class="text-red-600 text-sm bg-red-50 p-2 rounded border border-red-200">
            Please set your address first before booking.
          </div>
        @else
          <div class="text-sm">
            <span class="text-gray-600">Address:</span> 
            <span class="font-medium">{{ $primary->line1 }}{{ $primary->barangay ? ', '.$primary->barangay : '' }}{{ $primary->city ? ', '.$primary->city : '' }}{{ $primary->province ? ', '.$primary->province : '' }}{{ $primary->postal_code ? ', '.$primary->postal_code : '' }}</span>
          </div>
          <input type="hidden" name="address_id" value="{{ $primary->id }}">
        @endif
      </div>
      
      <!-- Date and Time Selection -->
      <div class="grid grid-cols-2 gap-3">
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
          <input type="text" id="date-picker" name="date" readonly placeholder="Select date" class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer" required>
          <i class="ri-calendar-line absolute right-3 top-8 text-gray-400 pointer-events-none"></i>
          <!-- Custom Date Picker -->
          <div id="date-picker-dropdown" class="fixed z-[9999] bg-white border border-gray-200 rounded-lg shadow-lg p-3 hidden" style="width: 240px;">
            <div class="flex items-center justify-between mb-3">
              <button type="button" id="prev-month" class="p-1 hover:bg-gray-100 rounded cursor-pointer">
                <i class="ri-arrow-left-s-line text-gray-600"></i>
              </button>
              <h3 id="current-month" class="font-semibold text-gray-800"></h3>
              <button type="button" id="next-month" class="p-1 hover:bg-gray-100 rounded cursor-pointer">
                <i class="ri-arrow-right-s-line text-gray-600"></i>
              </button>
            </div>
            <div class="grid grid-cols-7 gap-1 mb-2">
              <div class="text-center text-xs font-medium text-gray-500 py-1">Su</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">Mo</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">Tu</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">We</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">Th</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">Fr</div>
              <div class="text-center text-xs font-medium text-gray-500 py-1">Sa</div>
            </div>
            <div id="calendar-days" class="grid grid-cols-7 gap-1"></div>
            <div class="flex justify-between mt-3 pt-3 border-t">
              <button type="button" id="clear-date" class="text-sm text-gray-500 hover:text-gray-700 cursor-pointer">Clear</button>
              <button type="button" id="today-btn" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium cursor-pointer">Today</button>
            </div>
          </div>
        </div>
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
          <input type="text" id="time-picker" name="time" readonly placeholder="Select time" class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer" required>
          <i class="ri-time-line absolute right-3 top-8 text-gray-400 pointer-events-none"></i>
          <!-- Custom Time Picker -->
          <div id="time-picker-dropdown" class="fixed z-[9999] bg-white border border-gray-200 rounded-lg shadow-lg p-4 hidden" style="width: 200px;">
            <div class="flex items-center justify-center gap-2 mb-3">
              <select id="hour-select" class="border border-gray-300 rounded px-2 py-1 text-center w-16">
                <option value="">--</option>
                @for($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
              <span class="text-gray-500">:</span>
              <select id="minute-select" class="border border-gray-300 rounded px-2 py-1 text-center w-16">
                <option value="">--</option>
                @for($i = 0; $i < 60; $i += 15)
                  <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
              <select id="ampm-select" class="border border-gray-300 rounded px-2 py-1 text-center w-16">
                <option value="">--</option>
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
            <div class="flex justify-between pt-3 border-t">
              <button type="button" id="clear-time" class="text-sm text-gray-500 hover:text-gray-700 cursor-pointer">Clear</button>
              <button type="button" id="apply-time" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium cursor-pointer">Apply</button>
            </div>
          </div>
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
        <button type="submit" class="px-4 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-800 transition-colors duration-200">
          Book Now
        </button>
      </div>
    </form>
  </div>
  <script>
  // Custom Date and Time Picker Functionality
  let currentDate = new Date();
  let selectedDate = null;
  let selectedTime = null;

  // Date Picker Functions
  function initDatePicker() {
    const dateInput = document.getElementById('date-picker');
    const dateDropdown = document.getElementById('date-picker-dropdown');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    const clearBtn = document.getElementById('clear-date');

    // Check if elements exist
    if (!dateInput || !dateDropdown) {
      console.log('Date picker elements not found');
      return;
    }

    // Remove existing event listeners to prevent duplicates
    dateInput.removeEventListener('click', dateInput._dateClickHandler);
    
    // Create new event handler
    dateInput._dateClickHandler = (e) => {
      e.stopPropagation();
      dateDropdown.classList.toggle('hidden');
      if (!dateDropdown.classList.contains('hidden')) {
        // Position calendar directly below the input (like before) but with fixed positioning
        const inputRect = dateInput.getBoundingClientRect();
        const calendarWidth = 240;
        const calendarHeight = 320;
        
        // Position directly below the input field
        let top = inputRect.bottom + 4;
        let left = inputRect.left;
        
        // Adjust if calendar would go off right edge
        if (left + calendarWidth > window.innerWidth) {
          left = window.innerWidth - calendarWidth - 10;
        }
        
        // Adjust if calendar would go off left edge
        if (left < 10) {
          left = 10;
        }
        
        // Apply fixed positioning - always below the input
        dateDropdown.style.position = 'fixed';
        dateDropdown.style.top = top + 'px';
        dateDropdown.style.left = left + 'px';
        dateDropdown.style.right = 'auto';
        dateDropdown.style.bottom = 'auto';
        dateDropdown.style.transform = 'none';
        
        renderCalendar();
      }
    };

    // Add event listener
    dateInput.addEventListener('click', dateInput._dateClickHandler);

    // Navigation
    prevBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
    });

    nextBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
    });

    // Today button
    todayBtn.addEventListener('click', () => {
      const today = new Date();
      selectDate(today);
      dateDropdown.classList.add('hidden');
    });

    // Clear button
    clearBtn.addEventListener('click', () => {
      selectedDate = null;
      dateInput.value = '';
      dateInput.placeholder = 'Select date';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!dateInput.contains(e.target) && !dateDropdown.contains(e.target)) {
        dateDropdown.classList.add('hidden');
      }
    });
  }

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Update month display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    const calendarDays = document.getElementById('calendar-days');
    calendarDays.innerHTML = '';

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < startingDayOfWeek; i++) {
      const emptyDay = document.createElement('div');
      emptyDay.className = 'h-7';
      calendarDays.appendChild(emptyDay);
    }

    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
      const dayElement = document.createElement('button');
      dayElement.type = 'button';
      dayElement.className = 'h-7 w-7 text-xs rounded hover:bg-gray-100 transition-colors';
      dayElement.textContent = day;

      const currentDay = new Date(year, month, day);
      currentDay.setHours(0, 0, 0, 0);

      // Disable past dates
      if (currentDay < today) {
        dayElement.classList.add('text-gray-300', 'cursor-not-allowed');
        dayElement.disabled = true;
      } else {
        dayElement.classList.add('text-gray-700', 'hover:bg-emerald-100');
        
        // Highlight selected date
        if (selectedDate && currentDay.getTime() === selectedDate.getTime()) {
          dayElement.classList.add('bg-emerald-600', 'text-white', 'hover:bg-emerald-700');
        }

        // Highlight today
        if (currentDay.getTime() === today.getTime()) {
          dayElement.classList.add('font-semibold', 'ring-2', 'ring-emerald-200');
        }

        dayElement.addEventListener('click', () => {
          selectDate(currentDay);
          document.getElementById('date-picker-dropdown').classList.add('hidden');
        });
      }

      calendarDays.appendChild(dayElement);
    }
  }

  function selectDate(date) {
    selectedDate = date;
    const dateInput = document.getElementById('date-picker');
    const formattedDate = date.toLocaleDateString('en-CA'); // YYYY-MM-DD format
    dateInput.value = formattedDate;
    dateInput.placeholder = date.toLocaleDateString('en-US', { 
      weekday: 'short', 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
    renderCalendar(); // Re-render to update selection
  }

  // Time Picker Functions
  function initTimePicker() {
    const timeInput = document.getElementById('time-picker');
    const timeDropdown = document.getElementById('time-picker-dropdown');
    const hourSelect = document.getElementById('hour-select');
    const minuteSelect = document.getElementById('minute-select');
    const ampmSelect = document.getElementById('ampm-select');
    const applyBtn = document.getElementById('apply-time');
    const clearBtn = document.getElementById('clear-time');

    // Check if elements exist
    if (!timeInput || !timeDropdown) {
      console.log('Time picker elements not found');
      return;
    }

    // Remove existing event listeners to prevent duplicates
    timeInput.removeEventListener('click', timeInput._timeClickHandler);
    
    // Create new event handler
    timeInput._timeClickHandler = (e) => {
      e.stopPropagation();
      timeDropdown.classList.toggle('hidden');
      if (!timeDropdown.classList.contains('hidden')) {
        // Position time picker directly below the input with fixed positioning
        const inputRect = timeInput.getBoundingClientRect();
        const timePickerWidth = inputRect.width; // Use the same width as the input field
        const timePickerHeight = 120;
        
        // Position directly below the input field
        let top = inputRect.bottom + 4;
        let left = inputRect.left;
        
        // Adjust if time picker would go off right edge
        if (left + timePickerWidth > window.innerWidth) {
          left = window.innerWidth - timePickerWidth - 10;
        }
        
        // Adjust if time picker would go off left edge
        if (left < 10) {
          left = 10;
        }
        
        // Apply fixed positioning - always below the input
        timeDropdown.style.position = 'fixed';
        timeDropdown.style.top = top + 'px';
        timeDropdown.style.left = left + 'px';
        timeDropdown.style.width = timePickerWidth + 'px';
        timeDropdown.style.right = 'auto';
        timeDropdown.style.bottom = 'auto';
        timeDropdown.style.transform = 'none';
      }
    };

    // Add event listener
    timeInput.addEventListener('click', timeInput._timeClickHandler);

    // Apply time selection
    applyBtn.addEventListener('click', () => {
      const hour = hourSelect.value;
      const minute = minuteSelect.value;
      const ampm = ampmSelect.value;

      if (hour && minute && ampm) {
        selectedTime = `${hour}:${minute} ${ampm}`;
        timeInput.value = selectedTime;
        timeInput.placeholder = selectedTime;
        timeDropdown.classList.add('hidden');
      }
    });

    // Clear time
    clearBtn.addEventListener('click', () => {
      selectedTime = null;
      timeInput.value = '';
      timeInput.placeholder = 'Select time';
      hourSelect.value = '';
      minuteSelect.value = '';
      ampmSelect.value = '';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!timeInput.contains(e.target) && !timeDropdown.contains(e.target)) {
        timeDropdown.classList.add('hidden');
      }
    });
  }

  // Function to close the booking modal
  function closeBookingModal() {
    const modal = document.getElementById('booking-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Re-enable body scroll
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Reset pickers
    selectedDate = null;
    selectedTime = null;
    document.getElementById('date-picker').value = '';
    document.getElementById('time-picker').value = '';
    document.getElementById('date-picker-dropdown').classList.add('hidden');
    document.getElementById('time-picker-dropdown').classList.add('hidden');
  }

  // Event listener for opening the booking modal
  window.addEventListener('openBookingModal', function(e){
    const modal = document.getElementById('booking-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('booking_total').value = e.detail.total;
    document.getElementById('items_json').value = JSON.stringify(e.detail.items||[]);
    
    // Prevent background scroll
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '0px';
    
    // Initialize pickers when modal opens (with a small delay to ensure DOM is ready)
    setTimeout(() => {
      console.log('Initializing pickers...');
      console.log('Date picker element:', document.getElementById('date-picker'));
      console.log('Time picker element:', document.getElementById('time-picker'));
      initDatePicker();
      initTimePicker();
    }, 100);
  });

  // Initialize pickers on page load
  document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if elements exist
    if (document.getElementById('date-picker') && document.getElementById('time-picker')) {
      initDatePicker();
      initTimePicker();
    }
  });

  // Fallback: Direct event delegation for date picker
  document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'date-picker') {
      const dropdown = document.getElementById('date-picker-dropdown');
      const input = e.target;
      if (dropdown) {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
          // Position calendar directly below the input (like before) but with fixed positioning
          const inputRect = input.getBoundingClientRect();
          const calendarWidth = 240;
          const calendarHeight = 320;
          
          // Position directly below the input field
          let top = inputRect.bottom + 4;
          let left = inputRect.left;
          
          // Adjust if calendar would go off right edge
          if (left + calendarWidth > window.innerWidth) {
            left = window.innerWidth - calendarWidth - 10;
          }
          
          // Adjust if calendar would go off left edge
          if (left < 10) {
            left = 10;
          }
          
          // Apply fixed positioning - always below the input
          dropdown.style.position = 'fixed';
          dropdown.style.top = top + 'px';
          dropdown.style.left = left + 'px';
          dropdown.style.right = 'auto';
          dropdown.style.bottom = 'auto';
          dropdown.style.transform = 'none';
          
          renderCalendar();
        }
      }
    }
    
    if (e.target && e.target.id === 'time-picker') {
      const dropdown = document.getElementById('time-picker-dropdown');
      const input = e.target;
      if (dropdown) {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
          // Position time picker directly below the input with fixed positioning
          const inputRect = input.getBoundingClientRect();
          const timePickerWidth = inputRect.width; // Use the same width as the input field
          const timePickerHeight = 120;
          
          // Position directly below the input field
          let top = inputRect.bottom + 4;
          let left = inputRect.left;
          
          // Adjust if time picker would go off right edge
          if (left + timePickerWidth > window.innerWidth) {
            left = window.innerWidth - timePickerWidth - 10;
          }
          
          // Adjust if time picker would go off left edge
          if (left < 10) {
            left = 10;
          }
          
          // Apply fixed positioning - always below the input
          dropdown.style.position = 'fixed';
          dropdown.style.top = top + 'px';
          dropdown.style.left = left + 'px';
          dropdown.style.width = timePickerWidth + 'px';
          dropdown.style.right = 'auto';
          dropdown.style.bottom = 'auto';
          dropdown.style.transform = 'none';
        }
      }
    }
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

  // Get the primary address data at the top level - use the global variable
  const primaryAddressData = @json($GLOBALS['primaryAddress'] ?? null);
  
  // Debug: Check what the PHP $primary variable contains
  console.log('PHP $primary variable value:', @json($GLOBALS['primaryAddress'] ?? null));
  console.log('Full address object:', primaryAddressData);
  if (primaryAddressData) {
    console.log('Address ID:', primaryAddressData.id);
    console.log('Address coordinates:', primaryAddressData.latitude, primaryAddressData.longitude);
  }
  
  // Function to handle form submission confirmation
  function confirmBookingSubmission(event) {
    event.preventDefault();
    
    // Check if customer has a primary address by looking for the address_id input field
    // If the form shows an address, there will be a hidden input with address_id
    const addressInput = document.querySelector('input[name="address_id"]');
    const hasAddress = addressInput && addressInput.value && addressInput.value.trim() !== '';
    
    if (!hasAddress) {
      // Show address setup alert if no address exists
      Swal.fire({
        title: 'Address Required',
        html: `
          <div class="text-left">
            <p class="mb-3">You don't have an address set up yet. Please add your address in your profile page before making a booking.</p>
            <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg text-sm">
              <p class="text-blue-800"><strong>📍 Why do we need your address?</strong></p>
              <p class="text-blue-700 mt-1">We need your address to provide our cleaning services at your location.</p>
            </div>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Go to Profile',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect to profile page
          window.location.href = '{{ route("customer.profile") }}';
        }
      });
    } else {
      // Get the address from the form's display - look for the span after "Address:"
      let addressString = 'Address not found';
      
      // Always use the global address data for consistency
      let addressData = primaryAddressData;
      
      // Find the div that contains "Address:" and get the span after it
      const addressDivs = document.querySelectorAll('.text-sm');
      for (let div of addressDivs) {
        if (div.textContent.includes('Address:')) {
          const addressSpan = div.querySelector('span.font-medium');
          if (addressSpan) {
            // Clean up the address string by removing extra spaces around commas
            addressString = addressSpan.textContent.trim().replace(/\s*,\s*/g, ', ');
            break;
          }
        }
      }
      
      // If we still don't have the address from form display, build it from global data
      if (addressString === 'Address not found' && addressData && addressData.line1) {
        const parts = [];
        if (addressData.line1) parts.push(addressData.line1);
        if (addressData.barangay) parts.push(addressData.barangay);
        if (addressData.city) parts.push(addressData.city);
        if (addressData.province) parts.push(addressData.province);
        if (addressData.postal_code) parts.push(addressData.postal_code);
        addressString = parts.join(', ');
      }
      
      // Debug logging
      console.log('Address from form display:', addressString);
      console.log('Primary address data:', primaryAddressData);
      console.log('Address data for map:', addressData);
      console.log('Has postal code:', addressData ? addressData.postal_code : 'no addressData');
      console.log('Has coordinates:', addressData ? (addressData.latitude && addressData.longitude) : 'no addressData');
      console.log('Latitude value:', addressData ? addressData.latitude : 'no addressData');
      console.log('Longitude value:', addressData ? addressData.longitude : 'no addressData');
      console.log('Coordinates check:', addressData ? (addressData.latitude != null && addressData.longitude != null && addressData.latitude != 0 && addressData.longitude != 0) : false);
      
      // Additional debugging for coordinate issues
      if (addressData) {
        console.log('Raw latitude:', addressData.latitude, 'Type:', typeof addressData.latitude);
        console.log('Raw longitude:', addressData.longitude, 'Type:', typeof addressData.longitude);
        console.log('Latitude is zero?', addressData.latitude === 0);
        console.log('Longitude is zero?', addressData.longitude === 0);
        console.log('Latitude is null?', addressData.latitude === null);
        console.log('Longitude is null?', addressData.longitude === null);
        console.log('Parsed latitude:', parseFloat(addressData.latitude));
        console.log('Parsed longitude:', parseFloat(addressData.longitude));
        console.log('Is latitude NaN?', isNaN(parseFloat(addressData.latitude)));
        console.log('Is longitude NaN?', isNaN(parseFloat(addressData.longitude)));
      }
      
      // Build full address with postal code (ensure it's included if not already present)
      let fullAddressString = addressString;
      if (addressData && addressData.postal_code && !addressString.includes(addressData.postal_code)) {
        fullAddressString += `, ${addressData.postal_code}`;
      }
      
      console.log('Final address string:', fullAddressString);
      // Check if coordinates are valid (not null, not zero, and not empty)
      const hasValidCoordinates = addressData && 
        addressData.latitude != null && 
        addressData.longitude != null && 
        addressData.latitude !== 0 && 
        addressData.longitude !== 0 &&
        addressData.latitude !== '0' && 
        addressData.longitude !== '0' &&
        !isNaN(parseFloat(addressData.latitude)) && 
        !isNaN(parseFloat(addressData.longitude)) &&
        parseFloat(addressData.latitude) !== 0 &&
        parseFloat(addressData.longitude) !== 0;
      
      console.log('Will show view location button:', hasValidCoordinates);
      
      // Show confirmation modal with primary address details
      Swal.fire({
        title: 'Confirm Booking',
        html: `
          <div class="text-left">
            <p class="mb-3">Are you sure you want to submit this booking?</p>
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mb-4">
              <div class="flex items-start gap-2">
                <i class="ri-map-pin-line text-emerald-500 text-lg mt-0.5"></i>
                <div class="flex-1">
                  <p class="text-sm font-medium text-emerald-800 mb-1">Primary Address:</p>
                  <p class="text-sm text-emerald-700 mb-2">${fullAddressString}</p>
                  ${hasValidCoordinates ? `
                    <button type="button" onclick="openBookingAddressMap(${addressData.latitude}, ${addressData.longitude}, '${fullAddressString}')" 
                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-md hover:bg-emerald-700 transition-colors shadow-sm cursor-pointer">
                      <i class="ri-map-pin-line mr-1.5"></i>
                      View Location on Map
                    </button>
                  ` : `
                    <p class="text-xs text-blue-600 italic">Map location not available</p>
                  `}
                </div>
              </div>
            </div>
            <p class="text-sm text-gray-600">Is this your correct primary address for the service?</p>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Yes, Book Now!',
        denyButtonText: 'Change Address',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#10b981',
        denyButtonColor: '#3b82f6',
        cancelButtonColor: '#ef4444'
      }).then((result) => {
        if (result.isConfirmed) {
          // Submit the form
          event.target.submit();
        } else if (result.isDenied) {
          // Redirect to profile page to change address
          window.location.href = '{{ route("customer.profile") }}#addresses';
        }
      });
    }
    
    return false; // Prevent default form submission
  }
  
  // Function to open address map modal for booking confirmation
  function openBookingAddressMap(latitude, longitude, addressText) {
    const modal = document.getElementById('booking-address-map-modal');
    const addressEl = document.getElementById('bookingAddressLocationAddress');
    const mapContainer = document.getElementById('bookingAddressMap');
    
    // Show modal first
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Set address text
    addressEl.textContent = addressText || 'Address location';
    
    // Parse coordinates with better validation
    const lat = parseFloat(latitude);
    const lng = parseFloat(longitude);
    
    // Validate coordinates - if invalid, use default Naga City coordinates
    const validLat = !isNaN(lat) && lat !== 0 ? lat : 13.6218;
    const validLng = !isNaN(lng) && lng !== 0 ? lng : 123.1948;
    
    console.log('Opening map modal with coordinates:', validLat, validLng);
    console.log('Original coordinates:', latitude, longitude);
    
    // Clear any existing map content to ensure clean initialization
    if (mapContainer) {
      mapContainer.innerHTML = '';
    }
    
    // Initialize map after modal is visible
    setTimeout(() => {
      try {
        // Create new map instance
        window.bookingAddressMap = L.map('bookingAddressMap', {
          center: [validLat, validLng],
          zoom: 15,
          zoomControl: true
        });
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(window.bookingAddressMap);
        
        // Add marker
        window.bookingAddressMarker = L.marker([validLat, validLng], {
          title: addressText || 'Service Location'
        }).addTo(window.bookingAddressMap);
        
        // Add popup to marker
        window.bookingAddressMarker.bindPopup(`
          <div class="text-center">
            <strong>Service Location</strong><br>
            <small>${addressText || 'Address location'}</small>
          </div>
        `);
        
        // Ensure map renders properly after modal animation
        setTimeout(() => {
          if (window.bookingAddressMap) {
            window.bookingAddressMap.invalidateSize();
            console.log('Map initialized and size invalidated');
          }
        }, 200);
        
      } catch (error) {
        console.error('Error initializing map:', error);
        // Show error message in map container
        if (mapContainer) {
          mapContainer.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100 rounded border">
              <div class="text-center text-gray-600">
                <i class="ri-map-pin-line text-2xl mb-2"></i>
                <p class="text-sm">Map could not be loaded</p>
                <p class="text-xs text-gray-500">Location: ${addressText || 'Address location'}</p>
              </div>
            </div>
          `;
        }
      }
    }, 100);
  }
  
  // Function to close booking address map modal
  function hideBookingAddressMap() {
    const modal = document.getElementById('booking-address-map-modal');
    const mapContainer = document.getElementById('bookingAddressMap');
    
    // Hide modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Clean up map instance to prevent memory leaks
    if (window.bookingAddressMap) {
      try {
        window.bookingAddressMap.remove();
        window.bookingAddressMap = null;
        window.bookingAddressMarker = null;
        console.log('Map instance cleaned up');
      } catch (error) {
        console.error('Error cleaning up map:', error);
      }
    }
    
    // Clear map container content
    if (mapContainer) {
      mapContainer.innerHTML = '';
    }
  }
  </script>
</div>
@endpush

<!-- Booking Address Location Map Modal -->
<div id="booking-address-map-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]" onclick="hideBookingAddressMap()">
    <div class="bg-white rounded-xl w-full max-w-xl p-4 m-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-3">
            <div class="font-semibold text-lg">Service Address Location</div>
            <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="hideBookingAddressMap()">✕</button>
        </div>
        <div id="bookingAddressLocationAddress" class="text-sm mb-3 text-gray-700 bg-gray-50 p-2 rounded border"></div>
        <div id="bookingAddressMap" class="h-80 rounded border border-gray-300 bg-gray-100"></div>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button" onclick="hideBookingAddressMap()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

