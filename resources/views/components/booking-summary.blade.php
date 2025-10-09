{{-- Booking Summary Component --}}
<div class="space-y-6">
    {{-- Booking Header --}}
    <div class="bg-gradient-to-r from-emerald-50 to-blue-50 p-6 rounded-lg border border-emerald-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Booking #{{ $booking->code ?? ('B'.date('Y').str_pad($booking->id,3,'0',STR_PAD_LEFT)) }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ isset($booking->scheduled_start) && $booking->scheduled_start ? \Carbon\Carbon::parse($booking->scheduled_start)->format('M j, Y g:i A') : 'No scheduled time' }}
                </p>
            </div>
            <div class="text-right">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-purple-100 text-purple-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        'no_show' => 'bg-gray-100 text-gray-800'
                    ];
                @endphp
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$booking->status ?? 'pending'] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ($booking->status ?? 'pending') === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $booking->status ?? 'pending')) }}
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Customer Information</h4>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium text-gray-600">Name:</span> {{ $customer->name ?? 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">Phone:</span> {{ $customer->phone ?? 'N/A' }}</p>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Service Details</h4>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium text-gray-600">Total Amount:</span> ₱{{ number_format($booking->total_amount ?? 0, 2) }}</p>
                    <p><span class="font-medium text-gray-600">Payment Status:</span> 
                        <span class="px-2 py-1 text-xs rounded-full {{ ($booking->payment_status ?? 'pending') === 'approved' ? 'bg-green-100 text-green-800' : (($booking->payment_status ?? 'pending') === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($booking->payment_status ?? 'pending') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- Assigned Employees --}}
    @if($assignedEmployees && count($assignedEmployees) > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                <i class="ri-user-line mr-2 text-emerald-600"></i>
                Assigned Employees
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($assignedEmployees as $employee)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                            <i class="ri-user-line text-emerald-600"></i>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $employee->name }}</h5>
                            <p class="text-sm text-gray-600">{{ $employee->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Additional Information --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
            <i class="ri-information-line mr-2 text-emerald-600"></i>
            Additional Information
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p><span class="font-medium text-gray-600">Created:</span> {{ isset($booking->created_at) && $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('M j, Y g:i A') : 'N/A' }}</p>
                <p><span class="font-medium text-gray-600">Updated:</span> {{ isset($booking->updated_at) && $booking->updated_at ? \Carbon\Carbon::parse($booking->updated_at)->format('M j, Y g:i A') : 'N/A' }}</p>
            </div>
            <div>
                @if(isset($booking->notes) && $booking->notes)
                    <p><span class="font-medium text-gray-600">Notes:</span> {{ $booking->notes }}</p>
                @endif
                @if(isset($booking->special_instructions) && $booking->special_instructions)
                    <p><span class="font-medium text-gray-600">Special Instructions:</span> {{ $booking->special_instructions }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
