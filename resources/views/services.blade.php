@extends('layouts.landing')

@section('title','Our Services - CleanSaver Naga')

@section('content')
<div class="max-w-7xl mx-auto pt-20">
    {{-- Page Header --}}
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-extrabold text-emerald-900 mb-4">Our Services</h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Professional cleaning services tailored to your needs. Choose from our comprehensive range of services 
            designed to keep your spaces spotless and healthy.
        </p>
    </div>

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        @foreach($services as $service)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
            {{-- Service Image --}}
            <div class="aspect-[4/3] overflow-hidden">
                <img src="{{ asset('assets/' . $service['image']) }}" 
                     alt="{{ $service['name'] }}" 
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            </div>
            
            {{-- Service Content --}}
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service['name'] }}</h3>
                
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    {{ $service['description'] }}
                </p>
                
                {{-- Pricing Information --}}
                <div class="bg-emerald-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-emerald-700">Starting Price</span>
                        <span class="text-lg font-bold text-emerald-900">{{ $service['base_price_formatted'] }}</span>
                    </div>
                    <div class="flex items-center justify-center text-sm text-emerald-600">
                        <span>{{ ucfirst($service['pricing_type']) }}</span>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    @if($service['has_tiered_pricing'])
                        <button onclick="openPricingModal('{{ $service['name'] }}', {{ json_encode($service['pricing_tiers']) }})" 
                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            View Pricing
                        </button>
                        <a href="{{ route('login') }}" 
                           class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium text-center">
                            Book Now
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium text-center">
                            Book Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pricing Information Section --}}
    <div class="bg-gray-50 rounded-2xl p-8 mb-16">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Pricing Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-calculator-line text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Transparent Pricing</h3>
                    <p class="text-gray-600 text-sm">
                        All prices are clearly displayed with no hidden fees. Prices may vary based on size and specific requirements.
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-time-line text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Flexible Scheduling</h3>
                    <p class="text-gray-600 text-sm">
                        We work around your schedule. Book services at your convenience with our flexible timing options.
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="text-center">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-shield-check-line text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality Guarantee</h3>
                    <p class="text-gray-600 text-sm">
                        We stand behind our work with a satisfaction guarantee. If you're not happy, we'll make it right.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Call to Action Section --}}
    <div class="bg-emerald-700 rounded-2xl p-8 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
            Ready to book your cleaning service? Our team is ready to help you achieve a cleaner, healthier space.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" 
               class="bg-emerald-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-emerald-800 transition-colors">
                Book Service Now
            </a>
        </div>
        
        <div class="mt-6 text-emerald-100 text-sm">
            <p>📞 Call us: (+63) 995 112 0443</p>
            <p>📧 Email: cleansaverph.naga@gmail.com</p>
        </div>
    </div>
</div>

{{-- Pricing Modal --}}
<div id="pricingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900" id="pricingModalTitle">Service Pricing</h2>
                <button onclick="closePricingModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-semibold text-emerald-700">Type</th>
                            <th class="border border-gray-200 px-4 py-3 text-left text-sm font-semibold text-emerald-700">Price</th>
                        </tr>
                    </thead>
                    <tbody id="pricingTableBody">
                        <!-- Pricing rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closePricingModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <a href="{{ route('login') }}" 
                   class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    Book Now
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Pricing Modal Functions
function openPricingModal(serviceName, pricingTiers) {
    document.getElementById('pricingModalTitle').textContent = serviceName + ' - Pricing';
    
    const tableBody = document.getElementById('pricingTableBody');
    tableBody.innerHTML = '';
    
    pricingTiers.forEach(tier => {
        const row = document.createElement('tr');
        
        // Check if this is a section header (empty price)
        if (tier.price === '') {
            row.className = 'bg-blue-50 font-semibold';
            row.innerHTML = `
                <td class="border border-gray-200 px-4 py-3 text-sm text-blue-700" colspan="2">${tier.type}</td>
            `;
        } else {
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="border border-gray-200 px-4 py-3 text-sm text-gray-900">${tier.type}</td>
                <td class="border border-gray-200 px-4 py-3 text-sm font-semibold text-emerald-600">${tier.price}</td>
            `;
        }
        
        tableBody.appendChild(row);
    });
    
    document.getElementById('pricingModal').classList.remove('hidden');
    document.getElementById('pricingModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closePricingModal() {
    document.getElementById('pricingModal').classList.add('hidden');
    document.getElementById('pricingModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('pricingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePricingModal();
    }
});
</script>
@endpush

