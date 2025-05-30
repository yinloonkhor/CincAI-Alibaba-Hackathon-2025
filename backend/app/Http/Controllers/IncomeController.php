<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\UserIncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incomes = Auth::user()->incomes()
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $incomes
        ]);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string',
            'customCategory' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user = Auth::user();
        
        $categoryId = null;
    
        // Handle category
        
        if ($request->category === 'other') {
            
            // If it's "other", check if custom_category is provided
            if ($request->filled('customCategory')) {
                
                // Check if this custom category already exists for this user
                $existingCategory = UserIncomeCategory::where('user_id', $user->id)
                    ->where('name', $request->customCategory)
                    ->first();
                
                if ($existingCategory) {
                    $categoryId = $existingCategory->id;
                } else {
                    
                    try {
                        // Create new category with the custom name and default color
                        $newCategory = UserIncomeCategory::create([
                            'user_id' => $user->id,
                            'name' => $request->customCategory,
                            'color' => '#6B7280', // Default gray color for custom categories
                            'is_default' => false
                        ]);
                        
                        $categoryId = $newCategory->id;
                    } catch (\Exception $e) {
                        \Log::error('Failed to create custom category', [
                            'error' => $e->getMessage(),
                            'custom_category' => $request->customCategory
                        ]);
                    }
                }
            } 
        } else {
            
            try {
                // For predefined categories
                $category = UserIncomeCategory::firstOrCreate(
                    ['user_id' => $user->id, 'name' => $request->category],
                    [
                        'is_default' => true,
                        'color' => $this->getDefaultColorForCategory($request->category)
                    ]
                );
                
             
                $categoryId = $category->id;
            } catch (\Exception $e) {
                \Log::error('Failed to process predefined category', [
                    'error' => $e->getMessage(),
                    'category' => $request->category
                ]);
            }
        }
   
        try {
            $income = Income::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'amount' => $request->amount,
                'date' => $request->date,
                'category_id' => $categoryId,
                'description' => $request->description
            ]);
            
    
            // Load the category relationship
            $income->load('category');
    
            return response()->json([
                'success' => true,
                'data' => $income
            ], 201);
        } catch (\Exception $e) {
          
            return response()->json([
                'success' => false,
                'message' => 'Failed to create income record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get default color for a category.
     */
    private function getDefaultColorForCategory(string $category): string
    {
        $defaultColors = [
            'salary' => '#10B981', // green-500
            'freelance' => '#3B82F6', // blue-500
            'investment' => '#8B5CF6', // purple-500
            'rental' => '#F59E0B', // yellow-500
            'business' => '#6366F1', // indigo-500
            'other' => '#6B7280', // gray-500
        ];
        
        $color = $defaultColors[$category] ?? '#6B7280';
        
        return $color;
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $income = Auth::user()->incomes()->with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string',
            'customCategory' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $income = Auth::user()->incomes()->findOrFail($id);
        $user = Auth::user();
        $categoryId = null;

        // Handle category
        if ($request->category !== 'other') {
            // Find or create the category
            $category = UserIncomeCategory::firstOrCreate(
                ['user_id' => $user->id, 'name' => $request->category],
                ['is_default' => true]
            );
            $categoryId = $category->id;
        }

        $income->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'category_id' => $categoryId,
            'description' => $request->description
        ]);

        // Load the category relationship
        $income->load('category');

        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $income = Auth::user()->incomes()->findOrFail($id);
        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income deleted successfully'
        ]);
    }

    /**
     * Get income statistics.
     */
    public function statistics()
    {
        $user = Auth::user();
        $incomes = $user->incomes;

        $totalIncome = $incomes->sum('amount');
        
        // Current month income
        $currentMonth = now()->startOfMonth();
        $currentMonthIncome = $incomes
            ->filter(function ($income) use ($currentMonth) {
                return $income->date->gte($currentMonth);
            })
            ->sum('amount');

        // Average income per entry
        $averageIncome = $incomes->count() > 0 ? $totalIncome / $incomes->count() : 0;

        // Category distribution
        $categoryDistribution = [];
        $categories = $user->incomeCategories;
        
        foreach ($categories as $category) {
            $amount = $incomes
                ->where('category_id', $category->id)
                ->sum('amount');
                
            if ($amount > 0) {
                $categoryDistribution[] = [
                    'name' => $category->name,
                    'amount' => $amount,
                    'percentage' => $totalIncome > 0 ? ($amount / $totalIncome) * 100 : 0
                ];
            }
        }

        // Handle custom categories (other)
        $otherAmount = $incomes
            ->whereNull('category_id')
            ->sum('amount');
            
        if ($otherAmount > 0) {
            $categoryDistribution[] = [
                'name' => 'other',
                'amount' => $otherAmount,
                'percentage' => $totalIncome > 0 ? ($otherAmount / $totalIncome) * 100 : 0
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_income' => $totalIncome,
                'current_month_income' => $currentMonthIncome,
                'average_income' => $averageIncome,
                'category_distribution' => $categoryDistribution
            ]
        ]);
    }

/**
 * Generate income summary report for the user.
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function generateSummaryReport(Request $request)
{
    \Log::info('Generating income summary report', ['user_id' => Auth::id()]);
    
    try {
        $user = Auth::user();
        $taxYear = $request->input('taxYear', date('Y'));
        
        // Define the tax year period (assuming Jan 1 to Dec 31)
        $startDate = "{$taxYear}-01-01";
        $endDate = "{$taxYear}-12-31";
        
        \Log::info('Fetching income data for tax year', [
            'tax_year' => $taxYear,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Get all incomes for the specified tax year
        $incomes = $user->incomes()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();
        
        // Format incomes for the report
        $formattedIncomes = $incomes->map(function($income) {
            return [
                'source' => $income->title,
                'category' => $income->category ? $income->category->name : 'Uncategorized',
                'amount' => (float) $income->amount,
                'date' => $income->date
            ];
        });
        
        // Calculate total income
        $totalIncome = $incomes->sum('amount');
        
        \Log::info('Income data retrieved', [
            'count' => $incomes->count(),
            'total_amount' => $totalIncome
        ]);
        
        // Get deductions from receipt items
        $deductions = [];
        $totalDeductions = 0;
        
        // If you have a receipts relationship
        if (method_exists($user, 'receipts')) {
            \Log::info('Fetching deduction data from receipt items');
            
            // Get all receipts for the user in the specified tax year
            $receipts = $user->receipts()
                ->with(['items.category'])
                ->whereBetween('receipt_date', [$startDate, $endDate])
                ->get();
            
            $deductionsByCategory = [];
            
            // Process each receipt and its items
            foreach ($receipts as $receipt) {
                foreach ($receipt->items as $item) {
                    // Check if the item is deductible (deductibility_id = 1)
                    if ($item->deductibility_id == 1) {
                        // Calculate the total price for this item (unit_price × quantity)
                        $itemTotal = $item->unit_price * $item->quantity;
                        
                        // Get the category name
                        $categoryName = $item->category ? $item->category->name : 'Uncategorized';
                        
                        // Initialize the category in our tracking array if it doesn't exist
                        if (!isset($deductionsByCategory[$categoryName])) {
                            $deductionsByCategory[$categoryName] = 0;
                        }
                        
                        // Add this item's total to the category total
                        $deductionsByCategory[$categoryName] += $itemTotal;
                        
                        // Add to the overall total deductions
                        $totalDeductions += $itemTotal;
                    }
                }
            }
            
            // Format deductions for the report
            foreach ($deductionsByCategory as $category => $amount) {
                $deductions[] = [
                    'type' => 'Expense Deduction',
                    'category' => $category,
                    'amount' => $amount
                ];
            }
            
            \Log::info('Deduction data retrieved from receipt items', [
                'categories' => count($deductionsByCategory),
                'total_amount' => $totalDeductions
            ]);
        } else {
            \Log::info('No receipts relationship found on user model');
        }
        
        // Calculate taxable income
        $taxableIncome = max(0, $totalIncome - $totalDeductions);
        
        // Calculate estimated tax (simplified Malaysian progressive tax rates for example)
        $estimatedTax = $this->calculateEstimatedTax($taxableIncome);
        
        \Log::info('Tax calculation completed', [
            'taxable_income' => $taxableIncome,
            'estimated_tax' => $estimatedTax
        ]);
        
        // Prepare the report data
        $reportData = [
            'taxYear' => $taxYear,
            'user' => [
                'name' => $user->name,
                'taxId' => $user->tax_id ?? 'Not provided', // You might need to add this field to your users table
                'email' => $user->email,
                'phone' => $user->phone ?? 'Not provided' // You might need to add this field to your users table
            ],
            'incomes' => $formattedIncomes,
            'deductions' => $deductions,
            'totalIncome' => $totalIncome,
            'totalDeductions' => $totalDeductions,
            'taxableIncome' => $taxableIncome,
            'estimatedTax' => $estimatedTax
        ];
        
        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to generate income summary report', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate income summary report',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getIncomeSummary()
{
    $user = Auth::user();
    $totalIncome = $user->incomes->sum('amount');
 

    return response()->json([
        'annualIncome' => $totalIncome,
    ]);
}

/**
 * Calculate estimated tax based on taxable income.
 * This is a simplified version of Malaysian progressive tax rates.
 * 
 * @param float $taxableIncome
 * @return float
 */
private function calculateEstimatedTax($taxableIncome)
{
    // Simplified Malaysian tax brackets for 2023 (example)
    $taxBrackets = [
        [0, 5000, 0],
        [5001, 20000, 1],
        [20001, 35000, 3],
        [35001, 50000, 8],
        [50001, 70000, 13],
        [70001, 100000, 21],
        [100001, 250000, 24],
        [250001, 400000, 24.5],
        [400001, 600000, 25],
        [600001, 1000000, 26],
        [1000001, PHP_INT_MAX, 28]
    ];
    
    $tax = 0;
    $remainingIncome = $taxableIncome;
    
    foreach ($taxBrackets as $bracket) {
        [$min, $max, $rate] = $bracket;
        
        if ($remainingIncome <= 0) {
            break;
        }
        
        if ($remainingIncome > $min) {
            $taxableAmount = min($remainingIncome, $max) - $min;
            $tax += $taxableAmount * ($rate / 100);
            $remainingIncome -= $taxableAmount;
        }
    }
    
    return round($tax, 2);
}
}
