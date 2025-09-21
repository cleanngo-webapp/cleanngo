@extends('layouts.admin')

@section('title','Inventory')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Inventory Management</h1>

    {{-- Inventory Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
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

    {{-- Inventory Records Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Inventory Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage inventory items and track stock levels</p>
                </div>
                <button onclick="openAddModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                    <i class="ri-add-line mr-2"></i>
                    Add Item
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Re-Order Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->item_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($item->quantity, 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">₱{{ number_format($item->unit_price, 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($item->total_value, 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($item->reorder_level, 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->updated_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-box-3-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Inventory Items Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        No inventory items have been added yet. Click "Add Item" to start managing your inventory.
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        <button onclick="openAddModal()" 
                                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                                            <i class="ri-add-line mr-2"></i>
                                            Add First Item
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
                
                // Store original values for change detection
                window.originalEditValues = {
                    name: item.name,
                    category: item.category,
                    quantity: parseInt(item.quantity),
                    unit_price: item.unit_price,
                    reorder_level: parseInt(item.reorder_level),
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
    } else {
        // Disable button
        updateButton.disabled = true;
        updateButton.classList.add('opacity-50', 'cursor-not-allowed');
        updateButton.title = 'No changes detected. Please modify at least one field to enable update.';
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
                    showInventorySuccessAlert(data.message);
                    
                    // Close modal and reset form
                    closeAddModal();
                    
                    // Refresh the page after a short delay to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
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
                    showInventorySuccessAlert(data.message);
                    
                    // Close modal and reset form
                    closeEditModal();
                    
                    // Refresh the page after a short delay to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
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
                    Swal.fire('Deleted!', data.message, 'success').then(() => {
                        location.reload();
                    });
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
function showInventorySuccessAlert(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.top = '80px'; // Position below the navigation bar
    alert.style.transform = 'translateX(100%)';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-check-line text-xl"></i>
            <div>
                <div class="font-medium">${message}</div>
                <div class="text-sm opacity-90">Item Added Successfully</div>
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
</script>
@endsection


