<?php

namespace App\Http\Controllers;

use App\Models\UserIncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserIncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Auth::user()->incomeCategories()->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:user_income_categories,name,NULL,id,user_id,' . Auth::id(),
            'color' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = UserIncomeCategory::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'color' => $request->color,
            'is_default' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Auth::user()->incomeCategories()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Auth::user()->incomeCategories()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:user_income_categories,name,' . $id . ',id,user_id,' . Auth::id(),
            'color' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

       /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Auth::user()->incomeCategories()->findOrFail($id);
        
        // Check if this category is in use
        $inUse = $category->incomes()->exists();
        
        if ($inUse) {
            return response()->json([
                'success' => false,
                'message' => 'This category is in use and cannot be deleted.'
            ], 422);
        }
        
        // Don't allow deletion of default categories
        if ($category->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Default categories cannot be deleted.'
            ], 422);
        }
        
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
    
    /**
     * Get default categories or create them if they don't exist.
     */
    public function getDefaultCategories()
    {
        $user = Auth::user();
        $defaultCategories = ['salary', 'freelance', 'investment', 'rental', 'business', 'other'];
        $defaultColors = [
            'salary' => '#10B981', // green-500
            'freelance' => '#3B82F6', // blue-500
            'investment' => '#8B5CF6', // purple-500
            'rental' => '#F59E0B', // yellow-500
            'business' => '#6366F1', // indigo-500
            'other' => '#6B7280', // gray-500
        ];
        
        foreach ($defaultCategories as $categoryName) {
            UserIncomeCategory::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $categoryName
                ],
                [
                    'color' => $defaultColors[$categoryName] ?? null,
                    'is_default' => true
                ]
            );
        }
        
        $categories = $user->incomeCategories()->orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}

