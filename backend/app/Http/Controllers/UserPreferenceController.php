<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\PaymentMethod;
use App\Models\UserPreference;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        
        if (!isset($userData['data_filled'])) {
            $userData['data_filled'] = $user->data_filled ?? false;
        }
        
        return response()->json($userData);
    }
    
    public function getExpenseCategories(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            if ($userId) {
                $user = User::find($userId);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found'
                    ], 404);
                }
            }
            
            $categories = ExpenseCategory::where('is_default', true)
                ->when($userId, function($query) use ($userId) {
                    return $query->orWhere('user_id', $userId);
                })
                ->get();
            
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'user_id' => $userId 
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve expense categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeExpenseCategory(Request $request)
    {
        try {
            if (!$request->has('user_id') && $request->query('user_id')) {
                $request->merge(['user_id' => $request->query('user_id')]);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_default' => 'boolean',
                'user_id' => 'required|exists:users,id',
                'is_system' => 'boolean',
            ]);
            
            if (!isset($validatedData['is_default'])) {
                $validatedData['is_default'] = false;
            }
            
            $validatedData['is_system'] = false;
            
            $category = ExpenseCategory::create($validatedData);
            
            
            return response()->json($category, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create expense category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'businessMeals' => 'nullable|string',
            'businessTravel' => 'nullable|string',
            'nric' => 'nullable|string',
            'nricName' => 'nullable|string',
            'personalDeductions' => 'nullable',
            'phoneNumber' => 'nullable|string',
            'profession' => 'nullable|string',
            'statementMethod' => 'nullable|string',
            'tin' => 'nullable|string',
            'workLocation' => 'nullable|string',
            'age' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $user = User::find($userId);
        
        $user->update([
            'nric' => $request->nric,
            'name' => $request->nricName,
            'phone_number' => $request->phoneNumber,
            'tin' => $request->tin,
            'age' => $request->age,
        ]);
        
        $preferencesToSave = [
            'businessMeals' => $request->businessMeals,
            'businessTravel' => $request->businessTravel,
            'personalDeductions' => $request->personalDeductions,
            'profession' => $request->profession,
            'statementMethod' => $request->statementMethod,
            'workLocation' => $request->workLocation,
        ];
        
        $savedPreferences = [];
        
        foreach ($preferencesToSave as $question => $answer) {
            if ($answer !== null) {
                if (is_array($answer)) {
                    $answer = json_encode($answer);
                }
                
                UserPreference::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'question' => $question
                    ],
                    [
                        'answer' => $answer
                    ]
                );
                
                $savedPreferences[$question] = $answer;
            }
        }
        
        $user->update([
            'data_filled' => true
        ]);

        return response()->json([
            'message' => 'User preferences saved successfully',
            'user_data' => [
                'nric' => $user->nric,
                'nric_name' => $user->name,
                'phone_number' => $user->phone_number,
                'tin' => $user->tin,
                'age' => $user->age, 
            ],
            'preferences' => $savedPreferences
        ], 200);
    }

    public function getExpensesSummary(Request $request)
    {
        try {
            $userId = $request->input('user_id', Auth::id());
            
            // Validate user exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            // Get all receipt items for the user
            $receiptItems = ReceiptItem::select('receipt_items.*')
                ->join('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
                ->where('receipts.user_id', $userId)
                ->get();
            
            // Initialize counters
            $total = 0;
            $fullyDeductible = 0;
            $partiallyDeductible = 0;
            $nonDeductible = 0;
            
            // Process each receipt item
            foreach ($receiptItems as $item) {
                $itemTotal = $item->total_price;
                $total += $itemTotal;
                
                // Categorize based on deductibility_id
                // 1 = Fully deductible, 2 = Partially deductible, 3 = Non-deductible
                switch ($item->deductibility_id) {
                    case 1: // Fully deductible
                        $fullyDeductible += $itemTotal;
                        break;
                        
                    case 2: // Partially deductible
                        // Calculate the deductible amount based on percentage
                        $deductibleAmount = $itemTotal * ($item->deduction_percentage / 100);
                        $partiallyDeductible += $deductibleAmount;
                        break;
                        
                    case 3: // Non-deductible
                    default:
                        $nonDeductible += $itemTotal;
                        break;
                }
            }
            
            // Format the response with two decimal places
            return response()->json([
                'total' => number_format($total, 2, '.', ''),
                'fullyDeductible' => number_format($fullyDeductible, 2, '.', ''),
                'partiallyDeductible' => number_format($partiallyDeductible, 2, '.', ''),
                'nonDeductible' => number_format($nonDeductible, 2, '.', '')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve expense summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve expense summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecentExpenses(Request $request)
    {
        try {
            $userId = $request->input('user_id', Auth::id());
            
            // Validate user exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            // Query receipt items
            $recentExpenses = ReceiptItem::select(
                    'receipt_items.*',
                    'expense_categories.name as category_name',
                    'deductibility_types.name as deductibility_name',
                    'receipts.receipt_date as receipt_date' // Changed from 'receipts.date' to 'receipts.receipt_date'
                )
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'receipt_items.category_id')
                ->leftJoin('deductibility_types', 'deductibility_types.id', '=', 'receipt_items.deductibility_id')
                ->leftJoin('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
                ->where(function($query) use ($userId) {
                    // Either the receipt belongs to the user
                    $query->whereHas('receipt', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    // Or the receipt_id is null (standalone expenses)
                    ->orWhere(function($q) {
                        $q->whereNull('receipt_items.receipt_id');
                        // Add additional condition here if needed to link standalone expenses to users
                    });
                })
                ->orderBy('receipt_items.created_at', 'desc')
                ->limit(20) // Limit to 20 most recent expenses
                ->get();
            
            return response()->json([
                'success' => true,
                'expenses' => $recentExpenses
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve recent expenses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent expenses',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function storeExpenses(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:expense_categories,id',
            'deductibility_id' => 'nullable|integer|min:1|max:3',
            'deduction_percentage' => 'required_if:deductibility_id,2|nullable|numeric|min:0|max:100', // Added nullable
            'receipt_id' => 'nullable|integer', // Changed to nullable
            'quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create a vendor using the description as the name
            $vendor = \App\Models\Vendor::firstOrCreate(
                ['name' => $request->description],
                ['address' => null]
            );

            // Create a Receipt record
            $receipt = new \App\Models\Receipt();
            $receipt->user_id = Auth::id();
            $receipt->receipt_date = now();
            $receipt->total_amount = $request->total_price;
            $receipt->vendor_id = $vendor->id; // Use the newly created vendor's ID
            
            // Find a payment method with name 'Other' or create one if it doesn't exist
            $paymentMethod = \App\Models\PaymentMethod::firstOrCreate(
                ['name' => 'Other', 'user_id' => Auth::id()],
                ['description' => 'Other payment method', 'is_default' => false]
            );
            
            $receipt->payment_method_id = $paymentMethod->id;
            $receipt->receipt_number = null;
            $receipt->notes = null;
            
            $receipt->save();
            
            // Now create the receipt item
            $receiptItem = new ReceiptItem();
            $receiptItem->receipt_id = $receipt->id; 
            $receiptItem->description = $request->description;
            $receiptItem->total_price = $request->total_price;
            $receiptItem->category_id = $request->category_id;
            $receiptItem->deductibility_id = 3; 
            $receiptItem->deduction_percentage = 0;
            $receiptItem->quantity = $request->quantity ?? 1;
            $receiptItem->unit_price = $request->total_price;
            $receiptItem->notes = $request->notes;
            $receiptItem->save();

            return response()->json([
                'receipt' => $receipt,
                'receipt_item' => $receiptItem
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Failed to store expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to store expense',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateExpenses(Request $request, $id)
    {
        \Log::info('updateExpenses request to update expense with ID: ' . $id);
        \Log::info('updateExpenses data: ' . json_encode($request->all()));
        // Find the expense
        $expense = Expense::findOrFail($id);

        // Check if the expense belongs to the authenticated user
        if ($expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:expense_categories,id',
            'deductibility_id' => 'required|integer|min:1|max:3',
            'deduction_percentage' => 'required_if:deductibility_id,2|numeric|min:0|max:100',
            'receipt_id' => 'nullable|integer',
            'quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the expense
        $expense->description = $request->description;
        $expense->total_price = $request->total_price;
        $expense->category_id = $request->category_id;
        $expense->deductibility_id = $request->deductibility_id;
        $expense->deduction_percentage = $request->deduction_percentage ?? 100;
        $expense->receipt_id = $request->receipt_id;
        $expense->quantity = $request->quantity ?? 1;
        $expense->unit_price = $request->unit_price ?? $request->total_price;
        $expense->notes = $request->notes;
        $expense->save();

        return response()->json($expense);
    }
    
    public function getUserPreferences()
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $preferences = UserPreference::where('user_id', $userId)->get();
        
        $formattedPreferences = [];
        foreach ($preferences as $preference) {
            $answer = $preference->answer;
            
            if ($this->isJson($answer)) {
                $answer = json_decode($answer);
            }
            
            $formattedPreferences[$preference->question] = $answer;
        }
        
        $response = [
            'nric' => $user->nric,
            'nricName' => $user->nric_name,
            'phoneNumber' => $user->phone_number,
            'tin' => $user->tin,
            'age' => $user->age,
        ];

        \Log::info('Formatted Preferences:', ['response' => $response]);
        return response()->json(array_merge($response, $formattedPreferences));
    }
    
    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
