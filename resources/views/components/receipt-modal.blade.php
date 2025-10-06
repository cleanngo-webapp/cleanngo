{{-- 
    Reusable Receipt Modal Component
    
    This component displays a service receipt with proper categorization,
    clean naming, quantities, and square meters where applicable.
    
    Usage:
    @include('components.receipt-modal', [
        'modalId' => 'receipt-modal',
        'receiptData' => $receiptData,
        'bookingId' => $bookingId,
        'title' => 'Service Summary', // Optional: defaults to 'Service Summary'
        'showPaymentMethod' => false, // Optional: defaults to false
        'paymentMethod' => null // Optional: payment method to display
    ])
--}}

<div id="{{ $modalId }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]" onclick="closeReceiptOnBackdrop('{{ $modalId }}', event)">
    <div class="bg-white w-full max-w-md px-4 py-6 {{ ($title ?? 'Service Summary') === 'Receipt' ? 'receipt-border' : 'rounded-xl' }}" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-2">
            <div class="font-semibold">{{ $title ?? 'Service Summary' }}</div>
            <button class="cursor-pointer" onclick="closeReceipt('{{ $modalId }}')">✕</button>
        </div>
        <div id="{{ $modalId }}-body" class="text-sm space-y-1"></div>
        <div class="mt-4 flex justify-end">
            <button class="bg-emerald-700 text-white px-3 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeReceipt('{{ $modalId }}')">Close</button>
        </div>
    </div>
</div>

<script>
// Service categorization and naming mapping
const serviceCategories = {
    'sofa': {
        name: 'Sofa Deep Cleaning',
        items: {
            'sofa_1': '1-Seater Sofa',
            'sofa_2': '2-Seater Sofa', 
            'sofa_3': '3-Seater Sofa',
            'sofa_4': '4-Seater Sofa',
            'sofa_5': '5-Seater Sofa',
            'sofa_6': '6-Seater Sofa',
            'sofa_7': '7-Seater Sofa',
            'sofa_8': '8-Seater Sofa'
        }
    },
    'mattress': {
        name: 'Mattress Deep Cleaning',
        items: {
            'mattress_single': 'Single Mattress',
            'mattress_double': 'Double Mattress',
            'mattress_king': 'King Mattress',
            'mattress_california': 'California King Mattress'
        }
    },
    'carpet': {
        name: 'Carpet Deep Cleaning',
        items: {
            'carpet_sqft': 'Square Foot'
        }
    },
    'car': {
        name: 'Home Service Car Interior Detailing',
        items: {
            'car_sedan': 'Sedan',
            'car_suv': 'SUV',
            'car_van': 'Van',
            'car_coaster': 'Hatchback'
        }
    },
    'post_construction': {
        name: 'Post Construction Cleaning',
        items: {
            'post_construction_sqm': 'Square Meter'
        }
    },
    'disinfect': {
        name: 'Home/Office Disinfection',
        items: {
            'disinfect_sqm': 'Square Meter'
        }
    },
    'glass': {
        name: 'Glass Cleaning',
        items: {
            'glass_sqft': 'Square Foot'
        }
    },
    'house_cleaning': {
        name: 'House Cleaning',
        items: {
            'house_cleaning_sqm': 'Square Meter'
        }
    },
    'curtain_cleaning': {
        name: 'Curtain Cleaning',
        items: {
            'curtain_cleaning_yard': 'Yard'
        }
    }
};

// Function to categorize and format receipt items
function categorizeReceiptItems(items) {
    const categorized = {
        'sofa': [],
        'mattress': [],
        'car': [],
        'carpet': [],
        'post_construction': [],
        'disinfect': [],
        'glass': [],
        'house_cleaning': [],
        'curtain_cleaning': []
    };
    
    items.forEach(item => {
        const itemType = item.item_type || item.type || '';
        let categorized_item = false;
        
        // Check each category
        for (const [categoryKey, category] of Object.entries(serviceCategories)) {
            if (category.items[itemType]) {
                categorized[categoryKey].push({
                    ...item,
                    displayName: category.items[itemType]
                });
                categorized_item = true;
                break;
            }
        }
        
        // If not categorized, skip it (no "Other Services" section)
        if (!categorized_item) {
            console.warn('Uncategorized item:', itemType);
        }
    });
    
    return categorized;
}

// Function to format peso currency
function peso(v) {
    return 'PHP ' + Number(v || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Function to open receipt modal
function openReceipt(modalId, bookingId, receiptData, options = {}) {
    const data = receiptData[String(bookingId)] || receiptData[bookingId];
    const modal = document.getElementById(modalId);
    const body = document.getElementById(modalId + '-body');
    const showPaymentMethod = options.showPaymentMethod || false;
    const paymentMethod = options.paymentMethod || null;
    
    // Prevent body scrolling when modal is open
    document.body.style.overflow = 'hidden';
    
    // Add escape key listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReceipt(modalId);
        }
    });
    
    if (!data || !data.lines || data.lines.length === 0) {
        body.innerHTML = '<div class="text-sm text-gray-500">No items recorded for this booking.</div>';
    } else {
        // Categorize the items
        const categorized = categorizeReceiptItems(data.lines);
        let html = '';
        
        // Render each category
        Object.entries(categorized).forEach(([categoryKey, items]) => {
            if (items.length === 0) return;
            
            const category = serviceCategories[categoryKey];
            const categoryName = category ? category.name : 'Other Services';
            
            // Category header
            html += `<div class="mb-3">`;
            html += `<div class="font-semibold text-gray-800 border-b border-gray-200 pb-1 mb-2">${categoryName}</div>`;
            
            // Items in this category
            items.forEach(item => {
                const quantity = item.quantity || 1;
                const areaSqm = item.area_sqm;
                const unitPrice = item.unit_price || 0;
                const lineTotal = item.line_total || (unitPrice * quantity * (areaSqm || 1));
                
                // Build item description
                let itemDesc = item.displayName;
                
                // For area-based services, show quantity with proper unit
                if (['carpet', 'post_construction', 'disinfect', 'glass', 'house_cleaning', 'curtain_cleaning'].includes(categoryKey)) {
                    if (quantity > 0) {
                        const unit = categoryKey === 'carpet' || categoryKey === 'glass' ? 'Square Foot' : 
                                   categoryKey === 'curtain_cleaning' ? 'Yard' : 'Square Meter';
                        itemDesc += ` x ${quantity}`;
                    }
                } else {
                    // For other services, show quantity only if > 1
                    if (quantity > 1) {
                        itemDesc += ` x ${quantity}`;
                    }
                }
                
                html += `<div class="flex justify-between py-1">`;
                html += `<span class="text-gray-700">${itemDesc}</span>`;
                html += `<span class="font-medium">${peso(lineTotal)}</span>`;
                html += `</div>`;
            });
            
            html += `</div>`;
        });
        
        // Total
        html += `<div class="mt-3 pt-2 border-t border-gray-200">`;
        html += `<div class="flex justify-between font-semibold text-lg">`;
        html += `<span>Total</span>`;
        html += `<span>${peso(data.total || 0)}</span>`;
        html += `</div>`;
        
        // Payment method (for customer receipts)
        if (showPaymentMethod && paymentMethod) {
            html += `<div class="mt-2 pt-2 border-t border-gray-100">`;
            html += `<div class="flex justify-between text-sm text-gray-600">`;
            html += `<span><i class="ri-bank-card-line mr-1"></i>Payment Method:</span>`;
            html += `<span class="font-medium">${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</span>`;
            html += `</div>`;
            html += `</div>`;
        }
        
        html += `</div>`;
        
        body.innerHTML = html;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Function to close receipt modal
function closeReceipt(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Restore body scrolling when modal is closed
    document.body.style.overflow = '';
}

// Function to close modal when clicking on backdrop
function closeReceiptOnBackdrop(modalId, event) {
    // Only close if clicking on the backdrop (not the modal content)
    if (event.target === event.currentTarget) {
        closeReceipt(modalId);
    }
}
</script>
