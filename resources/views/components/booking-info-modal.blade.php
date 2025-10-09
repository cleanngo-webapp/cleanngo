{{-- Booking Info Modal Component --}}
<div id="{{ $modalId ?? 'booking-info-modal' }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
    <div class="bg-white rounded-xl w-full max-w-6xl p-4 m-4" style="max-height: 90vh; overflow-y: auto;">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-xl text-gray-900">Booking Information</div>
            <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeBookingInfoModal('{{ $modalId ?? 'booking-info-modal' }}')">✕</button>
        </div>
        
        {{-- Tab Navigation --}}
        <div class="flex mb-6 border-b border-gray-200">
            <button onclick="switchBookingInfoTab('summary', '{{ $modalId ?? 'booking-info-modal' }}')" 
                    class="booking-info-tab flex-1 px-4 py-3 text-sm font-medium rounded-t-lg transition-colors cursor-pointer bg-emerald-50 text-emerald-700 border-b-2 border-emerald-500" 
                    data-tab="summary">
                <i class="ri-file-list-line mr-2"></i>
                Summary
            </button>
            <button onclick="switchBookingInfoTab('location', '{{ $modalId ?? 'booking-info-modal' }}')" 
                    class="booking-info-tab flex-1 px-4 py-3 text-sm font-medium rounded-t-lg transition-colors cursor-pointer text-gray-500 hover:text-gray-700 border-b-2 border-emerald-500 hover:bg-gray-50" 
                    data-tab="location">
                <i class="ri-map-pin-line mr-2"></i>
                Location
            </button>
            <button onclick="switchBookingInfoTab('photos', '{{ $modalId ?? 'booking-info-modal' }}')" 
                    class="booking-info-tab flex-1 px-4 py-3 text-sm font-medium rounded-t-lg transition-colors cursor-pointer text-gray-500 hover:text-gray-700 border-b-2 border-emerald-500 hover:bg-gray-50" 
                    data-tab="photos">
                <i class="ri-image-line mr-2"></i>
                Photos (<span id="booking-photos-count">0</span>)
            </button>
        </div>
        
        {{-- Tab Content --}}
        <div class="booking-info-content">
            {{-- Summary Tab Content --}}
            <div id="booking-summary-content" class="booking-info-tab-content">
                <div class="flex items-center justify-center py-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <span class="ml-3 text-gray-500 text-sm">Loading booking summary...</span>
                </div>
            </div>
            
            
            {{-- Location Tab Content --}}
            <div id="booking-location-content" class="booking-info-tab-content hidden">
                <div class="flex items-center justify-center py-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <span class="ml-3 text-gray-500 text-sm">Loading location information...</span>
                </div>
            </div>

            {{-- Photos Tab Content --}}
            <div id="booking-photos-content" class="booking-info-tab-content hidden">
                <div class="flex items-center justify-center py-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <span class="ml-3 text-gray-500 text-sm">Loading photos...</span>
                </div>
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200">
            <button type="button" onclick="closeBookingInfoModal('{{ $modalId ?? 'booking-info-modal' }}')" 
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

{{-- CSS for loading animation --}}
<style>
    .loading-dots {
        animation: loading-dots 1.4s infinite ease-in-out both;
    }
    
    .loading-dots:nth-child(1) {
        animation-delay: -0.32s;
    }
    
    .loading-dots:nth-child(2) {
        animation-delay: -0.16s;
    }
    
    @keyframes loading-dots {
        0%, 80%, 100% {
            transform: scale(0);
        }
        40% {
            transform: scale(1);
        }
    }
    
    .booking-info-tab.active {
        background-color: #ecfdf5;
        color: #047857;
        border-bottom-color: #10b981;
    }
    
    .booking-info-tab-content {
        min-height: 400px;
    }
</style>

{{-- JavaScript for tab switching and modal management --}}
<script>
    let currentBookingInfoModal = null;
    let currentBookingIdModal = null;
    let currentUserType = 'admin';
    let bookingInfoData = {
        summary: null,
        location: null,
        photos: null
    };

    function openBookingInfoModal(modalId, bookingId, userType = 'admin') {
        currentBookingInfoModal = modalId;
        currentBookingIdModal = bookingId;
        currentUserType = userType;
        
        // Reset data
        bookingInfoData = {
            summary: null,
            location: null,
            photos: null
        };
        
        // Show modal
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Reset to summary tab
        switchBookingInfoTab('summary', modalId);
        
        // Load summary data immediately
        loadBookingSummary(bookingId, userType);
        
        // Store photos data for later use
        bookingInfoData.photos = null;
        const photosCountEl = document.getElementById('booking-photos-count');
        if (photosCountEl) { photosCountEl.textContent = '0'; }
    }

    function closeBookingInfoModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset state
        currentBookingInfoModal = null;
        currentBookingIdModal = null;
        bookingInfoData = {
            summary: null,
            location: null,
            photos: null
        };
    }

    function switchBookingInfoTab(tabName, modalId) {
        // Update tab buttons
        const tabs = document.querySelectorAll(`#${modalId} .booking-info-tab`);
        tabs.forEach(tab => {
            tab.classList.remove('active', 'bg-emerald-50', 'text-emerald-700', 'border-emerald-500');
            tab.classList.add('text-gray-500', 'hover:bg-gray-50');
        });
        
        const activeTab = document.querySelector(`#${modalId} .booking-info-tab[data-tab="${tabName}"]`);
        if (activeTab) {
            activeTab.classList.add('active', 'bg-emerald-50', 'text-emerald-700', 'border-emerald-500');
            activeTab.classList.remove('text-gray-500', 'hover:bg-gray-50');
        }
        
        // Update tab content
        const tabContents = document.querySelectorAll(`#${modalId} .booking-info-tab-content`);
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeContent = document.querySelector(`#${modalId} #booking-${tabName}-content`);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }
        
        // Load data for the tab if not already loaded
        if (tabName === 'location' && !bookingInfoData.location) {
            loadBookingLocation(currentBookingIdModal, currentUserType);
        } else if (tabName === 'photos' && !bookingInfoData.photos) {
            loadBookingPhotos(currentBookingIdModal, currentUserType);
        } else if (tabName === 'summary') {
            // Summary is already loaded, no need to reload
        }
    }

    function loadBookingSummary(bookingId, userType) {
        const content = document.querySelector(`#${currentBookingInfoModal} #booking-summary-content`);
        
        // Show loading state
        content.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <span class="ml-3 text-gray-500 text-sm">Loading booking summary...</span>
            </div>
        `;
        
        // Fetch summary data
        const url = userType === 'admin' ? `/admin/bookings/${bookingId}/summary` : `/employee/bookings/${bookingId}/summary`;
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bookingInfoData.summary = data.summary;
                content.innerHTML = data.html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-file-list-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Summary</h3>
                        <p class="text-sm text-gray-500">Unable to load booking summary. Please try again.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading booking summary:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-error-warning-line text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Summary</h3>
                    <p class="text-sm text-gray-500">An error occurred while loading the booking summary.</p>
                </div>
            `;
        });
    }


    function loadBookingLocation(bookingId, userType = 'admin') {
        const content = document.querySelector(`#${currentBookingInfoModal} #booking-location-content`);
        
        // Show loading state
        content.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <span class="ml-3 text-gray-500 text-sm">Loading location information...</span>
            </div>
        `;
        
        // Fetch location data
        const locationUrl = userType === 'admin' ? `/admin/bookings/${bookingId}/location` : `/employee/bookings/${bookingId}/location`;
        fetch(locationUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bookingInfoData.location = data.location;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Address</h4>
                            <p class="text-sm text-gray-700">${data.location.address || 'No address provided'}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div id="booking-location-map" class="h-80 rounded border border-gray-300 bg-gray-100"></div>
                        </div>
                    </div>
                `;
                
                // Initialize map
                setTimeout(() => {
                    initializeBookingLocationMap(data.location.lat, data.location.lng);
                }, 100);
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-map-pin-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Location Available</h3>
                        <p class="text-sm text-gray-500">This booking doesn't have location information.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading booking location:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-error-warning-line text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Location</h3>
                    <p class="text-sm text-gray-500">An error occurred while loading the location information.</p>
                </div>
            `;
        });
    }

    function loadBookingPhotos(bookingId, userType = 'admin') {
        const content = document.querySelector(`#${currentBookingInfoModal} #booking-photos-content`);
        
        // Show loading state
        content.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <span class="ml-3 text-gray-500 text-sm">Loading photos...</span>
            </div>
        `;
        
        // Fetch photos data
        const photosUrl = userType === 'admin' ? `/admin/bookings/${bookingId}/photos` : `/employee/bookings/${bookingId}/photos`;
        fetch(photosUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const photosCountEl = document.querySelector(`#${currentBookingInfoModal} #booking-photos-count`);
            if (photosCountEl && typeof data.count === 'number') {
                photosCountEl.textContent = String(data.count);
            }
            
            if (data.success) {
                bookingInfoData.photos = data.photos || [];
                
                if (!data.photos || data.photos.length === 0) {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-image-line text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Photos</h3>
                            <p class="text-sm text-gray-500">No photos were uploaded for this booking.</p>
                        </div>
                    `;
                    return;
                }
                
                const items = data.photos.map(photo => `
                    <a href="${photo.url}" target="_blank" class="group block">
                        <div class="overflow-hidden rounded border border-gray-200 bg-gray-50">
                            <img src="${photo.url}" alt="${photo.filename}" class="w-full h-48 md:h-40 object-cover group-hover:opacity-90 transition" />
                        </div>
                    </a>
                `).join('');
                
                content.innerHTML = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">${items}</div>
                `;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-error-warning-line text-2xl text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Photos</h3>
                        <p class="text-sm text-gray-500">Unable to load photos. Please try again.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading booking photos:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-error-warning-line text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Photos</h3>
                    <p class="text-sm text-gray-500">An error occurred while loading the photos.</p>
                </div>
            `;
        });
    }

    function initializeBookingLocationMap(lat, lng) {
        const mapElement = document.getElementById('booking-location-map');
        if (!mapElement) return;
        
        // Initialize map
        const map = L.map('booking-location-map').setView([lat || 13.0, lng || 122.0], (lat && lng) ? 15 : 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker if coordinates are valid
        if (lat && lng) {
            L.marker([lat, lng]).addTo(map);
        }
        
        // Invalidate size after a short delay
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }

</script>
