{{-- 
    Payroll Receipt Modal Component for Employees
    
    This component displays a payroll receipt with jagged edges design
    showing payroll details instead of booking details.
    
    Usage:
    @include('components.payroll-receipt-modal', [
        'modalId' => 'payroll-receipt-modal',
        'payrollData' => $payrollData,
        'bookingId' => $bookingId,
        'title' => 'Payroll Details'
    ])
--}}

<div id="{{ $modalId }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]" onclick="closePayrollReceiptOnBackdrop('{{ $modalId }}', event)">
    <div class="bg-white w-full max-w-md px-4 py-6 payroll-receipt-border" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-lg">{{ $title ?? 'Payroll Details' }}</div>
            <button class="cursor-pointer text-gray-500 hover:text-gray-700" onclick="closePayrollReceipt('{{ $modalId }}')">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        <div id="{{ $modalId }}-body" class="text-sm space-y-3"></div>
        <div class="mt-6 flex justify-end">
            <button class="bg-emerald-700 text-white px-4 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white transition-colors" onclick="closePayrollReceipt('{{ $modalId }}')">
                Close
            </button>
        </div>
    </div>
</div>

<style>
/* Payroll receipt with jagged edges */
.payroll-receipt-border {
    position: relative;
    background: white;
    border: 2px solid #10b981;
}

.payroll-receipt-border::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #10b981 25%, transparent 25%), 
                linear-gradient(-45deg, #10b981 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #10b981 75%), 
                linear-gradient(-45deg, transparent 75%, #10b981 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    z-index: -1;
}

/* Jagged edge effect */
.payroll-receipt-border::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: white;
    mask: 
        radial-gradient(circle at 0 0, transparent 8px, black 8px),
        radial-gradient(circle at 100% 0, transparent 8px, black 8px),
        radial-gradient(circle at 0 100%, transparent 8px, black 8px),
        radial-gradient(circle at 100% 100%, transparent 8px, black 8px);
    mask-size: 50% 50%;
    mask-position: 0 0, 100% 0, 0 100%, 100% 100%;
    mask-repeat: no-repeat;
    z-index: -1;
}
</style>

<script>
// Function to format peso currency
function peso(v) {
    return '₱' + Number(v || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Function to open payroll receipt modal
function openPayrollReceipt(modalId, bookingId, payrollData, options = {}) {
    const data = payrollData[String(bookingId)] || payrollData[bookingId];
    const modal = document.getElementById(modalId);
    const body = document.getElementById(modalId + '-body');
    
    // Prevent body scrolling when modal is open
    document.body.style.overflow = 'hidden';
    
    // Add escape key listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePayrollReceipt(modalId);
        }
    });
    
    if (!data) {
        body.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">No payroll details available for this booking.</div>';
    } else {
        let html = '';
        
        // Payroll header
        html += `<div class="text-center mb-4 pb-3 border-b border-gray-200">`;
        html += `<div class="text-lg font-bold text-emerald-700">PAYROLL RECEIPT</div>`;
        html += `<div class="text-sm text-gray-600 mt-1">${data.payroll_code || 'N/A'}</div>`;
        html += `</div>`;
        
        // Booking information
        html += `<div class="mb-4">`;
        html += `<div class="text-sm font-semibold text-gray-800 mb-2">Booking Information</div>`;
        html += `<div class="space-y-1 text-sm">`;
        html += `<div class="flex justify-between"><span class="text-gray-600">Booking ID:</span><span class="font-medium">${data.booking_code || 'N/A'}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-gray-600">Date:</span><span class="font-medium">${data.completed_date || 'N/A'}</span></div>`;
        html += `</div>`;
        html += `</div>`;
        
        // Payroll details
        html += `<div class="mb-4">`;
        html += `<div class="text-sm font-semibold text-gray-800 mb-2">Payroll Details</div>`;
        html += `                <div class="space-y-1 text-sm">`;
                html += `<div class="flex justify-between"><span class="text-gray-600">Amount:</span><span class="font-medium text-emerald-700">${data.payroll_amount ? peso(data.payroll_amount) : 'N/A'}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-gray-600">Payment Method:</span><span class="font-medium">${(data.payroll_method || 'N/A').charAt(0).toUpperCase() + (data.payroll_method || 'N/A').slice(1)}</span></div>`;
        html += `<div class="flex justify-between"><span class="text-gray-600">Status:</span><span class="font-medium ${data.payroll_status === 'paid' ? 'text-green-600' : 'text-orange-600'}">${(data.payroll_status || 'unpaid').charAt(0).toUpperCase() + (data.payroll_status || 'unpaid').slice(1)}</span></div>`;
        html += `</div>`;
        html += `</div>`;
        
        // Payment proof section (if available)
        if (data.payroll_proof) {
            html += `<div class="mb-4">`;
            html += `<div class="text-sm font-semibold text-gray-800 mb-2">Payment Proof</div>`;
            html += `<div class="text-center">`;
            html += `<img src="/storage/${data.payroll_proof}" alt="Payment Proof" class="max-w-full h-auto rounded-lg border border-gray-200" style="max-height: 200px;">`;
            html += `</div>`;
            html += `</div>`;
        }
        
        // Footer
        html += `<div class="text-center text-xs text-gray-500 mt-4 pt-3 border-t border-gray-200">`;
        html += `<div>Thank you for your service!</div>`;
        html += `<div class="mt-1">Generated on ${new Date().toLocaleDateString('en-PH')}</div>`;
        html += `</div>`;
        
        body.innerHTML = html;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Function to close payroll receipt modal
function closePayrollReceipt(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Restore body scrolling when modal is closed
    document.body.style.overflow = '';
}

// Function to close modal when clicking on backdrop
function closePayrollReceiptOnBackdrop(modalId, event) {
    // Only close if clicking on the backdrop (not the modal content)
    if (event.target === event.currentTarget) {
        closePayrollReceipt(modalId);
    }
}
</script>
