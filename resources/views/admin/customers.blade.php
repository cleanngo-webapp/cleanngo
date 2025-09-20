@extends('layouts.admin')

@section('title','Customers')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Manage Customers</h1>

    {{-- Search and Sort Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-customers" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search customers by Name, Customer ID, or Phone" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'customer_id') === 'customer_id' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('customer_id')">
                        <i class="ri-customer-line mr-2"></i>
                        Sort by Customer ID
                        <i class="ri-arrow-{{ ($sort ?? 'customer_id') === 'customer_id' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'customer_id') === 'name' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('name')">
                        <i class="ri-user-line mr-2"></i>
                        Sort by Name
                        <i class="ri-arrow-{{ ($sort ?? 'customer_id') === 'name' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
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
                <tbody id="customers-table-body" class="bg-white divide-y divide-gray-200">
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
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('showCustomerMap',{detail:{userId:{{ $cust->user_id }}}}))" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    View Location
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-user-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Customers Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No customers match your current filters. Try adjusting your search criteria.
                                        @else
                                            No customers have been registered yet. Customers will appear here once they create accounts.
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('sort'))
                                            <button onclick="clearFilters()" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                                <i class="ri-refresh-line mr-2"></i>
                                                Clear Filters
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-100">
            @isset($customers)
                {{ $customers->links() }}
            @endisset
        </div>
    </div>
    <div id="customer-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-xl p-4 m-4">
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-lg">Customer Location</div>
                <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="hideCustMap()">✕</button>
            </div>
            <div id="custLocationAddress" class="text-sm mb-3 text-gray-700 bg-gray-50 p-2 rounded border"></div>
            <div id="custLocationPhone" class="text-xs mb-3 text-gray-500"></div>
            <div id="customerMap" class="h-80 rounded border border-gray-300 bg-gray-100"></div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" onclick="hideCustMap()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    var custMap, custMarker;
    
    // Global variables for search and sort
    let currentSort = '{{ $sort ?? "customer_id" }}';
    let currentSortOrder = '{{ $sortOrder ?? "desc" }}';
    let searchTimeout;
    
    // Search and sort functionality
    function toggleSort(sortType) {
        if (currentSort === sortType) {
            // Toggle sort order if same sort type
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new sort type with default descending order (like payroll tables)
            currentSort = sortType;
            currentSortOrder = 'desc';
        }
        
        // Update button styles and icons
        updateSortButtons();
        
        // Perform search/sort
        performSearch();
    }
    
    function updateSortButtons() {
        const buttons = document.querySelectorAll('[onclick^="toggleSort"]');
        buttons.forEach(btn => {
            btn.classList.remove('bg-emerald-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
            
            // Update arrow icons
            const icon = btn.querySelector('i:last-child');
            if (btn.onclick.toString().includes(currentSort)) {
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                btn.classList.add('bg-emerald-600', 'text-white');
                icon.className = `ri-arrow-${currentSortOrder === 'desc' ? 'down' : 'up'}-line ml-2`;
            } else {
                icon.className = 'ri-arrow-down-line ml-2';
            }
        });
    }
    
    // Auto-search on input (with debounce)
    document.getElementById('search-customers').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch();
        }, 300); // 300ms delay for faster response
    });
    
    // AJAX search function
    function performSearch() {
        const searchTerm = document.getElementById('search-customers').value;
        const url = new URL('{{ route("admin.customers") }}', window.location.origin);
        
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        url.searchParams.set('sort', currentSort);
        url.searchParams.set('sort_order', currentSortOrder);
        
        // Show loading state with animated preloader
        const tableBody = document.getElementById('customers-table-body');
        const paginationContainer = document.getElementById('pagination-container');
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center">
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <p class="text-gray-500 text-sm">Searching...</p>
                </td>
            </tr>
        `;
        paginationContainer.innerHTML = '';
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse the response HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract table body content
                const newTableBody = doc.getElementById('customers-table-body');
                const newPagination = doc.getElementById('pagination-container');
                
                if (newTableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                    // Add fade-in animation to new content
                    tableBody.classList.add('fade-in-up');
                    setTimeout(() => tableBody.classList.remove('fade-in-up'), 500);
                }
                if (newPagination) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
                
                // Update URL without page refresh
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Search error:', error);
                tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
            });
    }
    
    // Clear all filters function
    function clearFilters() {
        // Clear search input
        const searchInput = document.getElementById('search-customers');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Reset sort
        currentSort = 'customer_id';
        currentSortOrder = 'desc';
        updateSortButtons();
        
        // Perform search to refresh results
        performSearch();
    }
    
    // Map functionality
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
            var lat = data?.latitude ?? 13.6218, lng = data?.longitude ?? 123.1948;
            if(!custMarker){ custMarker = L.marker([lat,lng]).addTo(custMap); } else { custMarker.setLatLng([lat,lng]); }
            custMap.setView([lat,lng], 15);
            setTimeout(function(){ if (custMap) custMap.invalidateSize(true); }, 100);
        }, 50);
    });
    </script>
   
    @endpush
</div>
@endsection


