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

<div id="{{ $modalId }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
    <div class="bg-white rounded-xl w-full max-w-md p-4">
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
        name: 'Sofa Cleaning',
        items: {
            'sofa_1_seater': '1-Seater Sofa',
            'sofa_2_seater': '2-Seater Sofa', 
            'sofa_3_seater': '3-Seater Sofa',
            'sofa_4_seater': '4-Seater Sofa',
            'sofa_5_seater': '5-Seater Sofa',
            'sofa_6_seater': '6-Seater Sofa',
            'sofa_7_seater': '7-Seater Sofa',
            'sofa_8_seater': '8-Seater Sofa',
            'sofa_l_shape': 'L-Shape Sofa',
            'sofa_cross': 'Cross Sofa'
        }
    },
    'mattress': {
        name: 'Mattress Cleaning',
        items: {
            'mattress_single': 'Single Mattress',
            'mattress_double': 'Double Mattress',
            'mattress_king': 'King Mattress',
            'mattress_california': 'California King Mattress'
        }
    },
    'car': {
        name: 'Car Cleaning',
        items: {
            'car_sedan': 'Sedan',
            'car_suv': 'SUV',
            'car_van': 'Van',
            'car_coaster': 'Coaster'
        }
    },
    'carpet': {
        name: 'Carpet Deep Cleaning',
        items: {
            'carpet_sqm': 'Service Area'
        }
    },
    'post_construction': {
        name: 'Post Construction Cleaning',
        items: {
            'post_construction_sqm': 'Service Area'
        }
    },
    'disinfect': {
        name: 'Enhanced Disinfection',
        items: {
            'disinfect_sqm': 'Service Area'
        }
    },
    'glass': {
        name: 'Glass Cleaning',
        items: {
            'glass_sqm': 'Service Area'
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
        'other': []
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
        
        // If not categorized, add to other
        if (!categorized_item) {
            categorized.other.push({
                ...item,
                displayName: itemType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
            });
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
                
                // For individual area-based service categories, show quantity and sqm
                if (['carpet', 'post_construction', 'disinfect', 'glass'].includes(categoryKey)) {
                    if (quantity > 0) {
                        itemDesc += ` x ${quantity}`;
                    }
                    if (areaSqm) {
                        itemDesc += ` (${areaSqm} sqm)`;
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
}
</script>
