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
                    <p><span class="font-medium text-gray-600">Name:</span> {{ ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '') ?: 'N/A' }}</p>
                    <p><span class="font-medium text-gray-600">Phone:</span> {{ $customer->phone ?? 'N/A' }}</p>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Service Details</h4>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium text-gray-600">Total Amount:</span> ₱{{ number_format(($booking->total_due_cents ?? 0) / 100, 2) }}</p>
                    <p><span class="font-medium text-gray-600">Payment Status:</span> 
                        @php
                            $paymentStatus = $booking->payment_status ?? 'pending';
                            $paymentStatusClasses = [
                                'approved' => 'bg-green-100 text-green-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'declined' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'unpaid' => 'bg-yellow-100 text-yellow-800'
                            ];
                            $paymentStatusClass = $paymentStatusClasses[$paymentStatus] ?? 'bg-yellow-100 text-yellow-800';
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $paymentStatusClass }}">
                            {{ ucfirst($paymentStatus) }}
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

    {{-- Service Summary --}}
    @if($items && count($items) > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                <i class="ri-service-line mr-2 text-emerald-600"></i>
                Service Summary
            </h4>
            
            @php
                // Service categorization mapping (same as receipt modal)
                $serviceCategories = [
                    'sofa' => [
                        'name' => 'Sofa Deep Cleaning',
                        'items' => [
                            'sofa_1' => '1-Seater Sofa',
                            'sofa_2' => '2-Seater Sofa', 
                            'sofa_3' => '3-Seater Sofa',
                            'sofa_4' => '4-Seater Sofa',
                            'sofa_5' => '5-Seater Sofa',
                            'sofa_6' => '6-Seater Sofa',
                            'sofa_7' => '7-Seater Sofa',
                            'sofa_8' => '8-Seater Sofa'
                        ]
                    ],
                    'mattress' => [
                        'name' => 'Mattress Deep Cleaning',
                        'items' => [
                            'mattress_single' => 'Single Mattress',
                            'mattress_double' => 'Double Mattress',
                            'mattress_queen' => 'Queen Mattress',
                            'mattress_king' => 'King Mattress'
                        ]
                    ],
                    'carpet' => [
                        'name' => 'Carpet Deep Cleaning',
                        'items' => [
                            'carpet_sqft' => 'Square Foot'
                        ]
                    ],
                    'car' => [
                        'name' => 'Home Service Car Interior Detailing',
                        'items' => [
                            'car_sedan' => 'Sedan',
                            'car_suv' => 'SUV',
                            'car_van' => 'Van',
                            'car_coaster' => 'Hatchback'
                        ]
                    ],
                    'post_construction' => [
                        'name' => 'Post Construction Cleaning',
                        'items' => [
                            'post_construction_sqm' => 'Square Meter'
                        ]
                    ],
                    'disinfect' => [
                        'name' => 'Home/Office Disinfection',
                        'items' => [
                            'disinfect_sqm' => 'Square Meter'
                        ]
                    ],
                    'glass' => [
                        'name' => 'Glass Cleaning',
                        'items' => [
                            'glass_sqft' => 'Square Foot'
                        ]
                    ],
                    'house_cleaning' => [
                        'name' => 'House Cleaning',
                        'items' => [
                            'house_cleaning_sqm' => 'Square Meter'
                        ]
                    ],
                    'curtain_cleaning' => [
                        'name' => 'Curtain Cleaning',
                        'items' => [
                            'curtain_cleaning_yard' => 'Yard'
                        ]
                    ]
                ];
                
                // Categorize items
                $categorized = [
                    'sofa' => [],
                    'mattress' => [],
                    'car' => [],
                    'carpet' => [],
                    'post_construction' => [],
                    'disinfect' => [],
                    'glass' => [],
                    'house_cleaning' => [],
                    'curtain_cleaning' => []
                ];
                
                foreach($items as $item) {
                    $itemType = $item->item_type ?? '';
                    $categorized_item = false;
                    
                    // Check each category
                    foreach($serviceCategories as $categoryKey => $category) {
                        if (isset($category['items'][$itemType])) {
                            $categorized[$categoryKey][] = (object) array_merge((array) $item, [
                                'displayName' => $category['items'][$itemType]
                            ]);
                            $categorized_item = true;
                            break;
                        }
                    }
                }
            @endphp
            
            <div class="space-y-4">
                @foreach($categorized as $categoryKey => $categoryItems)
                    @if(count($categoryItems) > 0)
                        <div>
                            <div class="font-semibold text-gray-800 border-b border-gray-200 pb-1 mb-2">
                                {{ $serviceCategories[$categoryKey]['name'] }}
                            </div>
                            <div class="space-y-1">
                                @foreach($categoryItems as $item)
                                    @php
                                        $quantity = $item->quantity ?? 1;
                                        $areaSqm = $item->area_sqm;
                                        $unitPrice = ($item->unit_price_cents ?? 0) / 100; // Convert cents to pesos
                                        $lineTotal = ($item->line_total_cents ?? 0) / 100; // Convert cents to pesos
                                    @endphp
                                    
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-700">
                                            @if(in_array($categoryKey, ['carpet', 'post_construction', 'disinfect', 'glass', 'house_cleaning', 'curtain_cleaning']))
                                                {{ $item->displayName }} x {{ $quantity }}
                                            @else
                                                {{ $item->displayName }}{{ $quantity > 1 ? ' x ' . $quantity : '' }}
                                            @endif
                                        </span>
                                        <span class="font-medium">₱{{ number_format($lineTotal, 2) }}</span>
                                    </div>
                                    
                                    @if(in_array($categoryKey, ['carpet', 'post_construction', 'disinfect', 'glass', 'house_cleaning', 'curtain_cleaning']))
                                        <div class="flex justify-between py-1 text-xs text-gray-500 ml-2">
                                            <span>
                                                {{ $quantity }} 
                                                @if($categoryKey === 'carpet' || $categoryKey === 'glass')
                                                    Square Foot
                                                @elseif($categoryKey === 'curtain_cleaning')
                                                    Yard
                                                @else
                                                    Square Meter
                                                @endif
                                                × ₱{{ number_format($unitPrice, 2) }}
                                            </span>
                                            <span></span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
