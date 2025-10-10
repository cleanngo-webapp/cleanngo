@extends('layouts.admin')

@section('title','Inventory')

@section('content')
{{-- Mobile-specific styles for inventory page responsiveness --}}
<style>
	/* Mobile responsive styles for inventory page */
	@media (max-width: 640px) {
		/* Ensure modals are mobile-friendly */
		#addModal .relative,
		#editModal .relative,
		#viewModal .relative,
		#transactionModal .relative {
			width: 95vw !important;
			max-width: 95vw !important;
			margin: 0.5rem !important;
		}
		
		/* Make modal content stack vertically on mobile */
		#addModal .grid-cols-1,
		#editModal .grid-cols-1 {
			grid-template-columns: 1fr !important;
		}
		
		/* Ensure table doesn't cause horizontal overflow */
		.overflow-x-auto {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Make action buttons more touch-friendly */
		.flex .inline-flex {
			min-height: 2.5rem;
		}
		
		/* Reduce card padding and spacing for mobile */
		.block.sm\\:hidden .p-2 {
			padding: 0.375rem !important;
		}
		
		.block.sm\\:hidden .space-y-3 > * + * {
			margin-top: 0.375rem !important;
		}
		
		/* Make the entire card container more compact */
		.block.sm\\:hidden {
			margin-left: -0.25rem !important;
			margin-right: -0.25rem !important;
		}
		
		/* Reduce border radius for more compact look */
		.block.sm\\:hidden .rounded-xl {
			border-radius: 0.375rem !important;
		}
		
		/* Make action buttons much more compact on mobile */
		.block.sm\\:hidden .flex.gap-1 {
			gap: 0.125rem !important;
		}
		
		.block.sm\\:hidden .flex-1 {
			flex: 1 1 0% !important;
			min-width: 0 !important;
		}
		
		/* Make buttons much smaller on mobile */
		.block.sm\\:hidden .px-1 {
			padding-left: 0.25rem !important;
			padding-right: 0.25rem !important;
		}
		
		.block.sm\\:hidden .py-1\\.5 {
			padding-top: 0.25rem !important;
			padding-bottom: 0.25rem !important;
		}
		
		/* Make text much smaller on mobile buttons */
		.block.sm\\:hidden .text-xs {
			font-size: 0.6rem !important;
			line-height: 0.875rem !important;
		}
		
		/* Hide button text on very small screens, show only icons */
		@media (max-width: 480px) {
			.block.sm\\:hidden .text-xs {
				font-size: 0 !important;
				line-height: 0 !important;
			}
			
			.block.sm\\:hidden .mr-0\\.5 {
				margin-right: 0 !important;
			}
			
			.block.sm\\:hidden .px-1 {
				padding-left: 0.25rem !important;
				padding-right: 0.25rem !important;
			}
			
			.block.sm\\:hidden .py-1 {
				padding-top: 0.125rem !important;
				padding-bottom: 0.125rem !important;
			}
		}
		
		/* Extra small screens - make buttons even more compact */
		@media (max-width: 360px) {
			.block.sm\\:hidden .flex.gap-0\\.5 {
				gap: 0.0625rem !important;
			}
			
			.block.sm\\:hidden .px-1 {
				padding-left: 0.125rem !important;
				padding-right: 0.125rem !important;
			}
		}
		
		/* Reduce grid gap on mobile */
		.block.sm\\:hidden .grid.gap-4 {
			gap: 0.5rem !important;
		}
		
		/* Make text smaller on mobile for more compact cards */
		.block.sm\\:hidden .text-sm {
			font-size: 0.8rem !important;
			line-height: 1.125rem !important;
		}
		
		.block.sm\\:hidden .text-xs {
			font-size: 0.7rem !important;
			line-height: 1rem !important;
		}
	}
</style>

<div class="max-w-7xl mx-auto px-0 sm:px-0">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-center">Inventory Management</h1>

    {{-- Inventory Statistics Cards - Responsive grid layout --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="ri-box-3-line text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Items</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-items">{{ $items->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="ri-alert-line text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-2xl font-bold text-gray-900" id="low-stock-items">{{ $items->where('status', 'Low Stock')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="ri-error-warning-line text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-gray-900" id="out-of-stock-items">{{ $items->where('status', 'Out of Stock')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="ri-money-dollar-circle-line text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Value</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-value">₱{{ number_format($items->sum('total_value'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Sort Section - Responsive layout --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-2 sm:p-6 border-b border-gray-100">
            {{-- Mobile: Stacked layout, Desktop: Side by side --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-inventory" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search inventory by Item Code, Item Name, or Category" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                {{-- Mobile: Full width buttons, Desktop: Compact buttons --}}
                <div class="grid grid-cols-2 sm:flex gap-2">
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'name') === 'name' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('name')">
                        <i class="ri-text mr-2"></i>
                        <span class="hidden sm:inline">Sort by Name</span>
                        <span class="sm:hidden">Name</span>
                        <i class="ri-arrow-{{ ($sort ?? 'name') === 'name' && ($sortOrder ?? 'asc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'name') === 'category' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('category')">
                        <i class="ri-folder-line mr-2"></i>
                        <span class="hidden sm:inline">Sort by Category</span>
                        <span class="sm:hidden">Category</span>
                        <i class="ri-arrow-{{ ($sort ?? 'name') === 'category' && ($sortOrder ?? 'asc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'name') === 'quantity' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('quantity')">
                        <i class="ri-number-1 mr-2"></i>
                        <span class="hidden sm:inline">Sort by Quantity</span>
                        <span class="sm:hidden">Quantity</span>
                        <i class="ri-arrow-{{ ($sort ?? 'name') === 'quantity' && ($sortOrder ?? 'asc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'name') === 'updated_at' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('updated_at')">
                        <i class="ri-calendar-line mr-2"></i>
                        <span class="hidden sm:inline">Sort by Date</span>
                        <span class="sm:hidden">Date</span>
                        <i class="ri-arrow-{{ ($sort ?? 'name') === 'updated_at' && ($sortOrder ?? 'asc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Records Section - Responsive header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-2 sm:p-6 border-b border-gray-100">
            {{-- Mobile: Stacked layout, Desktop: Side by side --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Inventory Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage inventory items and track stock levels</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <button onclick="openTransactionHistoryModal()" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer">
                        <i class="ri-history-line mr-2"></i>
                        <span class="hidden sm:inline">Transaction History</span>
                        <span class="sm:hidden">History</span>
                    </button>
                    <button onclick="openAddModal()" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                        <i class="ri-add-line mr-2"></i>
                        Add Item
                    </button>
                </div>
            </div>
        </div>
        {{-- Mobile Card View (hidden on larger screens) --}}
        <div class="block sm:hidden">
            @forelse($items as $item)
            <div class="p-2 border-b border-gray-100 last:border-b-0">
                <div class="space-y-3">
                    {{-- Item Header --}}
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-900">{{ $item->item_code }}</div>
                        @if($item->status === 'In Stock')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                In Stock
                            </span>
                        @elseif($item->status === 'Low Stock')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Low Stock
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        @endif
                    </div>
                    
                    {{-- Item Name and Category --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Item Name</div>
                            <div class="text-sm text-gray-900">{{ $item->name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Category</div>
                            <div class="text-sm text-gray-900">{{ $item->category }}</div>
                        </div>
                    </div>
                    
                    {{-- Quantity and Unit Price --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Quantity</div>
                            <div class="text-sm text-gray-900">{{ number_format($item->quantity, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Unit Price</div>
                            <div class="text-sm text-gray-900">₱{{ number_format($item->unit_price, 0) }}</div>
                        </div>
                    </div>
                    
                    {{-- Total Value and Re-order Level --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Total Value</div>
                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($item->total_value, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Re-order Level</div>
                            <div class="text-sm text-gray-900">{{ number_format($item->reorder_level, 0) }}</div>
                        </div>
                    </div>
                    
                    {{-- Updated Date --}}
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Last Updated</div>
                        <div class="text-sm text-gray-900">{{ $item->updated_at->format('M d, Y') }}</div>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-0.5">
                        <button onclick="openEditModal({{ $item->id }})" class="flex-1 inline-flex items-center justify-center px-1 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                            <i class="ri-edit-line mr-0.5"></i>
                            <span class="hidden xs:inline">Edit</span>
                        </button>
                        <button onclick="openViewModal({{ $item->id }})" class="flex-1 inline-flex items-center justify-center px-1 py-1 border border-emerald-300 shadow-sm text-xs font-medium rounded text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                            <i class="ri-eye-line mr-0.5"></i>
                            <span class="hidden xs:inline">View</span>
                        </button>
                        <button onclick="deleteItem({{ $item->id }}, this)" class="flex-1 inline-flex items-center justify-center px-1 py-1 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors cursor-pointer">
                            <i class="ri-delete-bin-line mr-0.5"></i>
                            <span class="hidden xs:inline">Delete</span>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <!-- Empty State Icon -->
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="ri-box-3-line text-2xl text-gray-400"></i>
                    </div>
                    
                    <!-- Empty State Content -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Inventory Items Found</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            @if(request()->has('search') || request()->has('sort'))
                                No inventory items match your current filters. Try adjusting your search criteria.
                            @else
                                No inventory items have been added yet. Click "Add Item" to start managing your inventory.
                            @endif
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            @if(request()->has('search') || request()->has('sort'))
                                <button onclick="clearInventoryFilters()" 
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                    <i class="ri-refresh-line mr-2"></i>
                                    Clear Filters
                                </button>
                            @endif
                            <button onclick="openAddModal()" 
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                                <i class="ri-add-line mr-2"></i>
                                @if(request()->has('search') || request()->has('sort'))
                                    Add New Item
                                @else
                                    Add First Item
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        {{-- Desktop Table View (hidden on mobile) --}}
        <div class="hidden sm:block overflow-x-auto shadow-sm rounded-lg border border-gray-200 inventory-table-container">
            <table class="w-full min-w-[950px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Item Code</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Item Name</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24 hidden sm:table-cell">Category</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Quantity</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24 hidden md:table-cell">Unit Price</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28 hidden lg:table-cell">Total Value</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24 hidden xl:table-cell">Re-Order</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24 hidden lg:table-cell">Updated</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventory-table-body" class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->item_code }}</div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="text-sm text-gray-900">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500 sm:hidden">{{ $item->category }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap hidden sm:table-cell">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($item->quantity, 0) }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-sm text-gray-900">₱{{ number_format($item->unit_price, 0) }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap hidden lg:table-cell">
                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($item->total_value, 0) }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap hidden xl:table-cell">
                            <div class="text-sm text-gray-900">{{ number_format($item->reorder_level, 0) }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            @if($item->status === 'In Stock')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @elseif($item->status === 'Low Stock')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap hidden lg:table-cell">
                            <div class="text-sm text-gray-900">{{ $item->updated_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button onclick="openEditModal({{ $item->id }})" class="inline-flex items-center justify-center w-8 h-8 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" title="Update">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button onclick="openViewModal({{ $item->id }})" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" title="View">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button onclick="deleteItem({{ $item->id }}, this)" class="inline-flex items-center justify-center w-8 h-8 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors cursor-pointer" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-3 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-box-3-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Inventory Items Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No inventory items match your current filters. Try adjusting your search criteria.
                                        @else
                                            No inventory items have been added yet. Click "Add Item" to start managing your inventory.
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('sort'))
                                            <button onclick="clearInventoryFilters()" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                                <i class="ri-refresh-line mr-2"></i>
                                                Clear Filters
                                            </button>
                                        @endif
                                        <button onclick="openAddModal()" 
                                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                                            <i class="ri-add-line mr-2"></i>
                                            @if(request()->has('search') || request()->has('sort'))
                                                Add New Item
                                            @else
                                                Add First Item
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            <div class="text-xs text-gray-600">
                System automatically handles: status computation based on quantity vs reorder level, low-stock notifications.
            </div>
        </div>
    </div>
</div>

{{-- Add Item Modal --}}
<div id="addModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-lg bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Add New Inventory Item</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="addForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Category</option>
                            <option value="Tools">Tools</option>
                            <option value="Machine">Machine</option>
                            <option value="Cleaning Agent">Cleaning Agent</option>
                            <option value="Consumables">Consumables</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="quantity" step="1" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (₱)</label>
                        <input type="number" name="unit_price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Re-order Level</label>
                        <input type="number" name="reorder_level" step="1" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none" placeholder="Optional notes about this item."></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeAddModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition-colors cursor-pointer">
                        Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Item Modal --}}
<div id="editModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-lg bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Update Inventory Item</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="editForm">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" id="edit_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Category</option>
                            <option value="Tools">Tools</option>
                            <option value="Machine">Machine</option>
                            <option value="Cleaning Agent">Cleaning Agent</option>
                            <option value="Consumables">Consumables</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" step="1" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (₱)</label>
                        <input type="number" name="unit_price" id="edit_unit_price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Re-order Level</label>
                        <input type="number" name="reorder_level" id="edit_reorder_level" step="1" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="edit_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer resize-none" placeholder="Optional notes about this item..."></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition-colors cursor-pointer">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Item Modal --}}
<div id="viewModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-lg bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">View Inventory Item</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div id="viewContent" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                <button onclick="closeViewModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Transaction History Modal --}}
<div id="transactionModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-0 border w-full max-w-6xl shadow-2xl rounded-lg bg-white" style="max-h: calc(100vh - 2rem);">
        <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-semibold text-gray-900">Inventory Transaction History</h3>
                <button onclick="closeTransactionHistoryModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <!-- Scrollable content area -->
            <div id="transaction-content" class="flex-1 overflow-y-auto p-6">
                <!-- Dynamic content will be loaded here -->
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end pt-4 border-t border-gray-200 p-6 flex-shrink-0 bg-gray-50">
                <button onclick="closeTransactionHistoryModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Search and Sort functionality
let currentSort = '{{ $sort ?? "name" }}';
let currentSortOrder = '{{ $sortOrder ?? "asc" }}';
let searchTimeout;

// Search functionality with AJAX
document.getElementById('search-inventory').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performInventorySearch();
    }, 300);
});

// Sort functionality
function toggleSort(sortField) {
    if (currentSort === sortField) {
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort = sortField;
        currentSortOrder = 'asc';
    }
    
    // Update button styles and icons
    updateSortButtons();
    
    // Perform search/sort
    performInventorySearch();
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
            icon.className = 'ri-arrow-up-line ml-2';
        }
    });
}

// AJAX search function for inventory
function performInventorySearch() {
    const searchTerm = document.getElementById('search-inventory').value;
    const url = new URL('{{ route("admin.inventory") }}', window.location.origin);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    url.searchParams.set('sort', currentSort);
    url.searchParams.set('sortOrder', currentSortOrder);
    
    // Show loading state
    const tableBody = document.getElementById('inventory-table-body');
    tableBody.innerHTML = `
        <tr>
            <td colspan="10" class="px-3 py-8 text-center">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <p class="text-gray-500 text-sm">Searching...</p>
            </td>
        </tr>
    `;
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.getElementById('inventory-table-body');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            
            // Update statistics cards
            const newStatsCards = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-6.mt-6');
            if (newStatsCards) {
                const currentStatsCards = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-6.mt-6');
                if (currentStatsCards) {
                    currentStatsCards.innerHTML = newStatsCards.innerHTML;
                }
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
        });
}

// Clear all filters function
function clearInventoryFilters() {
    // Clear search input
    const searchInput = document.getElementById('search-inventory');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset sort
    currentSort = 'name';
    currentSortOrder = 'asc';
    updateSortButtons();
    
    // Perform search to refresh results
    performInventorySearch();
}

// Modal functions
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.getElementById('addForm').reset();
}

function openEditModal(itemId) {
    // Fetch item data and populate form
    fetch(`/admin/inventory/${itemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.item;
                document.getElementById('edit_item_id').value = item.id;
                document.getElementById('edit_name').value = item.name;
                document.getElementById('edit_category').value = item.category;
                document.getElementById('edit_quantity').value = parseInt(item.quantity);
                document.getElementById('edit_unit_price').value = item.unit_price;
                document.getElementById('edit_reorder_level').value = parseInt(item.reorder_level);
                document.getElementById('edit_notes').value = item.notes || '';
                
                // Store original values for change detection with proper data types
                window.originalEditValues = {
                    name: item.name,
                    category: item.category,
                    quantity: parseInt(item.quantity) || 0,
                    unit_price: parseFloat(item.unit_price) || 0,
                    reorder_level: parseInt(item.reorder_level) || 0,
                    notes: item.notes || ''
                };
                
                // Disable update button initially
                const updateButton = document.querySelector('#editForm button[type="submit"]');
                updateButton.disabled = true;
                updateButton.classList.add('opacity-50', 'cursor-not-allowed');
                updateButton.title = 'No changes detected. Please modify at least one field to enable update.';
                
                // Add change detection to all form inputs
                setupEditFormChangeDetection();
                
                document.getElementById('editModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load item data', 'error');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    // Reset update button state
    const updateButton = document.querySelector('#editForm button[type="submit"]');
    if (updateButton) {
        updateButton.disabled = false;
        updateButton.classList.remove('opacity-50', 'cursor-not-allowed');
        updateButton.title = '';
    }
    // Clear original values
    window.originalEditValues = null;
}

// Setup change detection for edit form
function setupEditFormChangeDetection() {
    const form = document.getElementById('editForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        // Remove any existing listeners to prevent duplicates
        input.removeEventListener('input', checkForChanges);
        input.removeEventListener('change', checkForChanges);
        
        // Add new listeners
        input.addEventListener('input', checkForChanges);
        input.addEventListener('change', checkForChanges);
    });
}

// Check if any changes have been made to the edit form
function checkForChanges() {
    if (!window.originalEditValues) return;
    
    const currentValues = {
        name: document.getElementById('edit_name').value,
        category: document.getElementById('edit_category').value,
        quantity: parseInt(document.getElementById('edit_quantity').value) || 0,
        unit_price: parseFloat(document.getElementById('edit_unit_price').value) || 0,
        reorder_level: parseInt(document.getElementById('edit_reorder_level').value) || 0,
        notes: document.getElementById('edit_notes').value || ''
    };
    
    const hasChanges = Object.keys(currentValues).some(key => {
        return currentValues[key] !== window.originalEditValues[key];
    });
    
    const updateButton = document.querySelector('#editForm button[type="submit"]');
    if (hasChanges) {
        // Enable button
        updateButton.disabled = false;
        updateButton.classList.remove('opacity-50', 'cursor-not-allowed');
        updateButton.title = 'Update item with current changes';
        updateButton.innerHTML = 'Update Item';
    } else {
        // Disable button
        updateButton.disabled = true;
        updateButton.classList.add('opacity-50', 'cursor-not-allowed');
        updateButton.title = 'No changes detected. Please modify at least one field to enable update.';
        updateButton.innerHTML = 'Update Item';
    }
}

function openViewModal(itemId) {
    // Fetch item data and display
    fetch(`/admin/inventory/${itemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.item;
                const statusColor = item.status === 'In Stock' ? 'green' : 
                                  item.status === 'Low Stock' ? 'yellow' : 'red';
                
                document.getElementById('viewContent').innerHTML = `
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item Code</label>
                            <p class="text-sm text-gray-900">${item.item_code}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="text-sm text-gray-900">${item.category}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Item Name</label>
                            <p class="text-sm text-gray-900">${item.name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <p class="text-sm text-gray-900">${parseInt(item.quantity)}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                            <p class="text-sm text-gray-900">₱${parseFloat(item.unit_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Value</label>
                            <p class="text-sm font-medium text-gray-900">₱${(parseFloat(item.unit_price) * parseInt(item.quantity)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Re-order Level</label>
                            <p class="text-sm text-gray-900">${parseInt(item.reorder_level)}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-${statusColor}-100 text-${statusColor}-800">
                                ${item.status}
                            </span>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="text-sm text-gray-900">${new Date(item.created_at).toLocaleDateString()}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="text-sm text-gray-900">${new Date(item.updated_at).toLocaleDateString()}</p>
                        </div>
                        ${item.notes ? `
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">${item.notes}</p>
                            </div>
                        </div>
                        ` : `
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-sm text-gray-500 italic">No notes available for this item.</p>
                            </div>
                        </div>
                        `}
                    </div>
                `;
                document.getElementById('viewModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to load item data', 'error');
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}


// Form submissions
document.getElementById('addForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data for confirmation
    const formData = new FormData(this);
    const name = formData.get('name');
    const category = formData.get('category');
    const quantity = formData.get('quantity');
    const unitPrice = formData.get('unit_price');
    const reorderLevel = formData.get('reorder_level');
    const notes = formData.get('notes');
    
    // Show loading state on the submit button before confirmation
    const submitButton = document.querySelector('#addForm button[type="submit"]');
    const originalButtonContent = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Processing...';
    
    // Show confirmation dialog with item details
    Swal.fire({
        title: 'Confirm Add Item?',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Are you sure you want to add this inventory item?</strong></p>
                <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Item Name:</span>
                        <span class="text-gray-900">${name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Category:</span>
                        <span class="text-gray-900">${category}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Quantity:</span>
                        <span class="text-gray-900">${parseInt(quantity)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Unit Price:</span>
                        <span class="text-gray-900">₱${parseFloat(unitPrice).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Re-order Level:</span>
                        <span class="text-gray-900">${parseInt(reorderLevel)}</span>
                    </div>
                    ${notes ? `
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Notes:</span>
                        <span class="text-gray-900 text-right max-w-xs">${notes}</span>
                    </div>
                    ` : ''}
                </div>
                <p class="mt-3 text-sm text-gray-600">Please verify all details are correct before proceeding.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Add Item',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Update button text for actual submission
            submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Adding Item...';
            
            fetch('/admin/inventory', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success alert that auto-disappears
                    showInventorySuccessAlert(data.message, 'Added');
                    
                    // Close modal and reset form
                    closeAddModal();
                    
                    // Refresh the table data via AJAX instead of page reload
                    setTimeout(() => {
                        refreshInventoryTable();
                    }, 1000);
                } else {
                    // Handle validation errors
                    showInventoryErrorAlert(data.message || 'An error occurred while creating the inventory item.');
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showInventoryErrorAlert('An error occurred while creating the inventory item. Please try again.');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonContent;
            });
        } else {
            // Reset button if user cancels
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonContent;
        }
    });
});

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const itemId = document.getElementById('edit_item_id').value;
    const name = formData.get('name');
    const category = formData.get('category');
    const quantity = formData.get('quantity');
    const unitPrice = formData.get('unit_price');
    const reorderLevel = formData.get('reorder_level');
    const notes = formData.get('notes');
    
    // Check if there are actual changes before showing loading state
    if (!window.originalEditValues) {
        showInventoryErrorAlert('No changes detected. Please modify at least one field to enable update.');
        return;
    }
    
    const currentValues = {
        name: formData.get('name'),
        category: formData.get('category'),
        quantity: parseInt(formData.get('quantity')) || 0,
        unit_price: parseFloat(formData.get('unit_price')) || 0,
        reorder_level: parseInt(formData.get('reorder_level')) || 0,
        notes: formData.get('notes') || ''
    };
    
    const hasChanges = Object.keys(currentValues).some(key => {
        return currentValues[key] !== window.originalEditValues[key];
    });
    
    if (!hasChanges) {
        showInventoryErrorAlert('No changes detected. Please modify at least one field to enable update.');
        return;
    }
    
    // Show loading state on the submit button before confirmation
    const submitButton = document.querySelector('#editForm button[type="submit"]');
    const originalButtonContent = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Processing...';
    
    // Show confirmation dialog with item details
    Swal.fire({
        title: 'Confirm Edit Item?',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Are you sure you want to update this inventory item?</strong></p>
                <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Item Name:</span>
                        <span class="text-gray-900">${name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Category:</span>
                        <span class="text-gray-900">${category}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Quantity:</span>
                        <span class="text-gray-900">${parseInt(quantity)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Unit Price:</span>
                        <span class="text-gray-900">₱${parseFloat(unitPrice).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Re-order Level:</span>
                        <span class="text-gray-900">${parseInt(reorderLevel)}</span>
                    </div>
                    ${notes ? `
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Notes:</span>
                        <span class="text-gray-900 text-right max-w-xs">${notes}</span>
                    </div>
                    ` : ''}
                </div>
                <p class="mt-3 text-sm text-gray-600">Please verify all details are correct before proceeding.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Update Item',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Update button text for actual submission
            submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Updating Item...';
            
            fetch(`/admin/inventory/${itemId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success alert that auto-disappears
                    showInventorySuccessAlert(data.message, 'Updated');
                    
                    // Close modal and reset form
                    closeEditModal();
                    
                    // Refresh the table data via AJAX instead of page reload
                    setTimeout(() => {
                        refreshInventoryTable();
                    }, 1000);
                } else {
                    // Handle validation errors
                    showInventoryErrorAlert(data.message || 'An error occurred while updating the inventory item.');
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showInventoryErrorAlert('An error occurred while updating the inventory item. Please try again.');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonContent;
            });
        } else {
            // Reset button if user cancels
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonContent;
        }
    });
});

// Delete function
function deleteItem(itemId, buttonElement) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state on the delete button
            const deleteButton = buttonElement || document.querySelector(`button[onclick="deleteItem(${itemId})"]`);
            const originalButtonContent = deleteButton.innerHTML;
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<div class="w-4 h-4 border-2 border-red-300 border-t-transparent rounded-full animate-spin"></div>';
            
            fetch(`/admin/inventory/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success alert that auto-disappears
                    showInventorySuccessAlert(data.message, 'Deleted');
                    
                    // Remove the table row immediately for better UX
                    const tableRow = deleteButton.closest('tr');
                    if (tableRow) {
                        tableRow.remove();
                    }
                    
                    // Refresh the table data via AJAX to update statistics
                    setTimeout(() => {
                        refreshInventoryTable();
                    }, 1000);
                } else {
                    Swal.fire('Error', data.message, 'error');
                    // Reset button on error
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to delete item', 'error');
                // Reset button on error
                deleteButton.disabled = false;
                deleteButton.innerHTML = originalButtonContent;
            });
        }
    });
}

// Show inventory success alert that auto-disappears
function showInventorySuccessAlert(message, action = 'Added') {
    const alert = document.createElement('div');
    alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.top = '80px'; // Position below the navigation bar
    alert.style.transform = 'translateX(100%)';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-check-line text-xl"></i>
            <div>
                <div class="font-medium">${message}</div>
                <div class="text-sm opacity-90">Item ${action} Successfully</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Animate in
    setTimeout(() => {
        alert.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }, 3000);
}

// Show inventory error alert
function showInventoryErrorAlert(message) {
    Swal.fire({
        title: 'Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK'
    });
}

// Refresh inventory table after operations
function refreshInventoryTable() {
    const url = new URL('{{ route("admin.inventory") }}', window.location.origin);
    
    // Show loading state
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.querySelector('.px-6.py-4.border-t.border-gray-100');
    
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="px-3 py-8 text-center">
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <p class="text-gray-500 text-sm">Refreshing inventory...</p>
                </td>
            </tr>
        `;
    }
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.querySelector('tbody');
            const newStatsCards = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-6.mt-6');
            
            if (newTableBody && tableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            
            // Update statistics cards
            if (newStatsCards) {
                const currentStatsCards = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-6.mt-6');
                if (currentStatsCards) {
                    currentStatsCards.innerHTML = newStatsCards.innerHTML;
                }
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Refresh error:', error);
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="10" class="px-3 py-4 text-center text-sm text-red-500">Error refreshing table</td></tr>';
            }
        });
}

// Transaction History Modal Functions
function openTransactionHistoryModal() {
    const modal = document.getElementById('transactionModal');
    const content = document.getElementById('transaction-content');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="flex justify-center items-center space-x-2 mb-4">
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
            </div>
            <p class="text-gray-500 text-sm">Loading transaction history...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch transaction history
    fetch('/admin/inventory/transactions', {
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
                // Process transactions if any exist
                if (data.transactions && data.transactions.length > 0) {
                    let transactionHtml = '<div class="overflow-x-auto"><table class="w-full">';
                transactionHtml += `
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                data.transactions.forEach(transaction => {
                    const dateTime = new Date(transaction.transaction_at).toLocaleString();
                    const typeClass = transaction.transaction_type === 'borrow' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
                    const typeText = transaction.transaction_type === 'borrow' ? 'Borrowed' : 'Returned';
                    
                    transactionHtml += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${dateTime}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">${transaction.employee_name}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${transaction.item_name}</div>
                                        <div class="text-sm text-gray-500">${transaction.item_category} • ${transaction.item_code}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">
                                    ${typeText}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseInt(transaction.quantity)}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${transaction.booking_code ? `<span class="text-blue-600 font-medium">#${transaction.booking_code}</span>` : '-'}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                <div class="truncate">${transaction.notes || '-'}</div>
                            </td>
                        </tr>
                    `;
                });
                
                transactionHtml += '</tbody></table></div>';
                
                // Add statistics summary
                const statsHtml = `
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="ri-download-line text-blue-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Total Borrowed</p>
                                    <p class="text-xl font-bold text-gray-900">${data.stats.total_borrowed}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <i class="ri-upload-line text-green-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-600">Total Returned</p>
                                    <p class="text-xl font-bold text-gray-900">${data.stats.total_returned}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                    <i class="ri-user-line text-gray-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                                    <p class="text-xl font-bold text-gray-900">${data.stats.active_employees}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                    <i class="ri-calendar-line text-orange-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-orange-600">This Month</p>
                                    <p class="text-xl font-bold text-gray-900">${data.stats.this_month}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                    content.innerHTML = statsHtml + transactionHtml;
                    
                } else {
                    // No transactions found
                    const statsHtml = `
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <i class="ri-download-line text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-600">Total Borrowed</p>
                                        <p class="text-xl font-bold text-gray-900">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                                        <i class="ri-upload-line text-green-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-600">Total Returned</p>
                                        <p class="text-xl font-bold text-gray-900">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                        <i class="ri-user-line text-gray-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Active Employees</p>
                                        <p class="text-xl font-bold text-gray-900">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                        <i class="ri-calendar-line text-orange-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-orange-600">This Month</p>
                                        <p class="text-xl font-bold text-gray-900">0</p>
                                    </div>
                                </div>
;">
                    `;
                    
                    content.innerHTML = statsHtml + `
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-history-line text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Transaction History</h3>
                            <p class="text-sm text-gray-500">No equipment borrowing or returning transactions have been recorded yet.</p>
                        </div>
                    `;
                    
                }
                
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-history-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Transaction History</h3>
                        <p class="text-sm text-gray-500">No equipment borrowing or returning transactions have been recorded yet.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading transaction history:', error);
            content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading transaction history.</div>';
        });
}

function closeTransactionHistoryModal() {
    const modal = document.getElementById('transactionModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@push('styles')
<style>
/* Loading dots animation for AJAX operations */
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

/* Responsive table improvements */
@media (max-width: 640px) {
    .inventory-table-container {
        font-size: 0.875rem;
    }
    
    .inventory-table-container th,
    .inventory-table-container td {
        padding: 0.5rem 0.75rem;
    }
}

/* Better table scrolling on mobile */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@endsection


