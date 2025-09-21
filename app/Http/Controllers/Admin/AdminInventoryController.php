<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminInventoryController extends Controller
{
    /**
     * Display the inventory management page
     */
    public function index()
    {
        $items = InventoryItem::active()->orderBy('created_at', 'desc')->get();
        
        return view('admin.inventory', compact('items'));
    }

    /**
     * Store a newly created inventory item
     */
    public function store(Request $request)
    {
        // Validate the request data (removed item_code validation since it will be auto-generated)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:Tools,Machine,Cleaning Agent,Consumables',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Auto-generate item code in format: I2025XXX (I + year + 3-digit unique number)
            $itemCode = $this->generateItemCode();
            
            // Create the item with auto-generated code
            $itemData = $request->all();
            $itemData['item_code'] = $itemCode;
            
            $item = InventoryItem::create($itemData);
            
            return response()->json([
                'success' => true,
                'message' => 'Inventory item created successfully!',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique item code in format: I2025XXX
     */
    private function generateItemCode()
    {
        $year = now()->format('Y');
        $prefix = 'I' . $year;
        
        // Get the highest existing code for this year
        $lastItem = InventoryItem::where('item_code', 'like', $prefix . '%')
            ->orderBy('item_code', 'desc')
            ->first();
        
        if ($lastItem) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastItem->item_code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            // First item for this year
            $newNumber = 1;
        }
        
        // Ensure the number is 3 digits with leading zeros
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified inventory item
     */
    public function show($id)
    {
        $item = InventoryItem::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }

    /**
     * Update the specified inventory item
     */
    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        // Validate the request data (item_code is not editable, so removed from validation)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:Tools,Machine,Cleaning Agent,Consumables',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Inventory item updated successfully',
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified inventory item
     */
    public function destroy($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Inventory item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inventory statistics for dashboard
     */
    public function getStats()
    {
        $totalItems = InventoryItem::active()->count();
        $lowStockItems = InventoryItem::active()->lowStock()->count();
        $outOfStockItems = InventoryItem::active()->outOfStock()->count();
        $totalValue = InventoryItem::active()->get()->sum('total_value');

        return response()->json([
            'success' => true,
            'stats' => [
                'total_items' => $totalItems,
                'low_stock_items' => $lowStockItems,
                'out_of_stock_items' => $outOfStockItems,
                'total_value' => number_format($totalValue, 2)
            ]
        ]);
    }
}
