<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Vendor;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\DeductibilityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receipts = Receipt::where('user_id', Auth::id())
            ->with(['vendor', 'paymentMethod'])
            ->orderBy('receipt_date', 'desc')
            ->paginate(15);
        
        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $categories = ExpenseCategory::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
        $deductibilityTypes = DeductibilityType::all();
        
        return view('receipts.create', compact('vendors', 'categories', 'paymentMethods', 'deductibilityTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'receipt_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:10240',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.category_id' => 'nullable|exists:expense_categories,id',
            'items.*.deductibility_id' => 'nullable|exists:deductibility_types,id',
            'items.*.deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        // Create vendor if it doesn't exist
        if (!$request->vendor_id && $request->new_vendor_name) {
            $vendor = Vendor::create([
                'name' => $request->new_vendor_name,
                'address' => $request->new_vendor_address,
            ]);
            $validated['vendor_id'] = $vendor->id;
        }

        // Create receipt
        $validated['user_id'] = Auth::id();
        $receipt = Receipt::create($validated);

        // Create receipt items
        foreach ($validated['items'] as $itemData) {
            $receipt->items()->create($itemData);
        }

        // Handle receipt image
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts/' . Auth::id(), 'public');
            $receipt->images()->create([
                'image_path' => $path,
                'original_filename' => $request->file('receipt_image')->getClientOriginalName(),
                'mime_type' => $request->file('receipt_image')->getMimeType(),
                'file_size' => $request->file('receipt_image')->getSize(),
            ]);
        }

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt)
    {
        $this->authorize('view', $receipt);
        
        $receipt->load(['vendor', 'paymentMethod', 'items.category', 'items.deductibilityType', 'images']);
        
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receipt $receipt)
    {
        $this->authorize('update', $receipt);
        
        $receipt->load(['items.category', 'items.deductibilityType', 'images']);
        
        $vendors = Vendor::orderBy('name')->get();
        $categories = ExpenseCategory::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
        $deductibilityTypes = DeductibilityType::all();
        
        return view('receipts.edit', compact('receipt', 'vendors', 'categories', 'paymentMethods', 'deductibilityTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        $this->authorize('update', $receipt);
        
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'receipt_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:10240',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:receipt_items,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.category_id' => 'nullable|exists:expense_categories,id',
            'items.*.deductibility_id' => 'nullable|exists:deductibility_types,id',
            'items.*.deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        // Create vendor if it doesn't exist
        if (!$request->vendor_id && $request->new_vendor_name) {
            $vendor = Vendor::create([
                'name' => $request->new_vendor_name,
                'address' => $request->new_vendor_address,
            ]);
            $validated['vendor_id'] = $vendor->id;
        }

        // Update receipt
        $receipt->update($validated);

        // Get existing item IDs
        $existingItemIds = $receipt->items->pluck('id')->toArray();
        $updatedItemIds = [];

        // Update or create receipt items
        foreach ($validated['items'] as $itemData) {
            if (isset($itemData['id'])) {
                $item = $receipt->items->find($itemData['id']);
                if ($item) {
                    $item->update($itemData);
                    $updatedItemIds[] = $item->id;
                }
            } else {
                $item = $receipt->items()->create($itemData);
                $updatedItemIds[] = $item->id;
            }
        }

        // Delete items that were not updated
        $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            $receipt->items()->whereIn('id', $itemsToDelete)->delete();
        }

        // Handle receipt image
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts/' . Auth::id(), 'public');
            $receipt->images()->create([
                'image_path' => $path,
                'original_filename' => $request->file('receipt_image')->getClientOriginalName(),
                'mime_type' => $request->file('receipt_image')->getMimeType(),
                'file_size' => $request->file('receipt_image')->getSize(),
            ]);
        }

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt)
    {
        $this->authorize('delete', $receipt);
        
        // Delete associated images from storage
        foreach ($receipt->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    /**
     * Process receipt with AI.
     */
    public function processWithAi(Receipt $receipt)
    {
        $this->authorize('update', $receipt);
        
        // This would be implemented with your AI service
        // For now, just a placeholder
        
        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt processed with AI successfully.');
    }

    /**
 * Store receipt data from frontend JSON.
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function storeFromJson(Request $request)
{
    \Log::info($request->all());
    // Validate the incoming request
    $request->validate([
        'data' => 'required|string',
        'image' => 'nullable|file|max:10240', // 10MB max file size
    ]);

    
    \Log::info('Received JSON data: ' . $request->data);

    try {
        // Decode the JSON data
        $receiptData = json_decode($request->data, true);
        
        if (!$receiptData) {
            return response()->json(['error' => 'Invalid JSON data'], 400);
        }

        // Validate the decoded data
        $this->validateReceiptData($receiptData);

        // Find or create vendor
        $vendor = null;
        if (!empty($receiptData['vendor'])) {
            $vendor = Vendor::firstOrCreate(
                ['name' => $receiptData['vendor']],
                ['name' => $receiptData['vendor']]
            );
        }

        // Find or create payment method (default if not specified)
        $paymentMethod = PaymentMethod::where('user_id', Auth::id())
            ->where('name', 'Other')
            ->first();
            
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::create([
                'user_id' => Auth::id(),
                'name' => 'Other'
            ]);
        }

        // Create the receipt
        $receipt = Receipt::create([
            'user_id' => Auth::id(),
            'vendor_id' => $vendor ? $vendor->id : null,
            'receipt_date' => $receiptData['date'] ?? now()->format('Y-m-d'),
            'total_amount' => $receiptData['total'] ?? 0,
            'payment_method_id' => $paymentMethod->id,
            'notes' => $receiptData['notes'] ?? null,
        ]);

        // Process receipt items
        if (!empty($receiptData['items']) && is_array($receiptData['items'])) {
            foreach ($receiptData['items'] as $itemData) {
                // Find or create category
                $category = null;
                if (!empty($itemData['category'])) {
                    $category = ExpenseCategory::firstOrCreate(
                        [
                            'name' => $itemData['category'],
                            'user_id' => Auth::id()
                        ],
                        [
                            'name' => $itemData['category'],
                            'user_id' => Auth::id()
                        ]
                    );
                }

                // Find deductibility type
                $deductibilityType = null;
                \Log::info($itemData['is_deductible']);

                if (isset($itemData['is_deductible'])) {
                    $deductibilityType = DeductibilityType::where('name', $itemData['is_deductible'] ? 'Fully Deductible' : 'Non-Deductible')->first();
                }

                // Create receipt item
                $receipt->items()->create([
                    'description' => $itemData['name'] ?? 'Unnamed Item',
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_price' => isset($itemData['price']) ? ($itemData['price'] / ($itemData['quantity'] ?? 1)) : 0,
                    'total_price' => $itemData['price'] ?? 0,
                    'category_id' => $category ? $category->id : null,
                    'deductibility_id' => $deductibilityType ? $deductibilityType->id : null,
                    'deduction_percentage' => $itemData['is_deductible'] ? 100 : 0,
                ]);
            }
        }

        // Handle receipt image/PDF
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('receipts/' . Auth::id(), 'public');
            
            $receipt->images()->create([
                'image_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipt saved successfully',
            'receipt_id' => $receipt->id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save receipt',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Validate receipt data from JSON.
 * 
 * @param array $data
 * @throws \Exception
 */
private function validateReceiptData($data)
{
    // Basic validation
    if (!isset($data['total']) || !is_numeric($data['total'])) {
        throw new \Exception('Receipt total amount is required and must be a number');
    }
    
    if (isset($data['date']) && !strtotime($data['date'])) {
        throw new \Exception('Receipt date is invalid');
    }
    
    if (isset($data['items']) && !is_array($data['items'])) {
        throw new \Exception('Receipt items must be an array');
    }
    
    // Validate items if present
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $index => $item) {
            if (!isset($item['name']) || empty($item['name'])) {
                throw new \Exception("Item at index $index must have a name");
            }
            
            if (isset($item['price']) && !is_numeric($item['price'])) {
                throw new \Exception("Item price at index $index must be a number");
            }
            
            if (isset($item['quantity']) && !is_numeric($item['quantity'])) {
                throw new \Exception("Item quantity at index $index must be a number");
            }
        }
    }
}

}
