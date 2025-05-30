<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\UserIncomeCategory; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        \Log::info($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Use a database transaction to ensure all operations succeed or fail together
        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Seed default expense categories and payment methods for the new user
            $this->seedDefaultDataForUser($user->id);
            
            DB::commit();
            
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seed default expense categories and payment methods for a new user
     *
     * @param int $userId
     * @return void
     */
    private function seedDefaultDataForUser($userId)
    {
        // Get system default categories (those with null user_id and is_system=true)
        $defaultCategories = ExpenseCategory::whereNull('user_id')
            ->where('is_system', true)
            ->get(['name', 'description']);

        // If no system categories exist, use hardcoded defaults
        if ($defaultCategories->isEmpty()) {
            $defaultCategories = collect([
                ['name' => 'Parents Medical Carer', 'description' => 'Medical expenses for parents'],
                ['name' => 'Disabled Equipment', 'description' => 'Equipment for disabled persons'],
                ['name' => 'Basic Education Fees', 'description' => 'Primary and secondary education'],
                ['name' => 'Higher Education Fees', 'description' => 'University and college education'],
                ['name' => 'Self Enhancement Courses', 'description' => 'Skills development courses'],
                ['name' => 'Serious Medical Expenses', 'description' => 'Major medical treatments'],
                ['name' => 'Fertility Treatment', 'description' => 'IVF and related procedures'],
                ['name' => 'Vaccination', 'description' => 'Immunization expenses'],
                ['name' => 'Medical Examination', 'description' => 'Health check-ups'],
                ['name' => 'Covid-19 Detection', 'description' => 'Testing and related expenses'],
                ['name' => 'Mental Health Care', 'description' => 'Psychiatric and counseling services'],
                ['name' => 'Learning Disability Assessment', 'description' => 'Diagnostic services'],
                ['name' => 'Learning Disability Treatment', 'description' => 'Therapy and support'],
                ['name' => 'Reading Materials', 'description' => 'Books and educational materials'],
                ['name' => 'Electronic Gadget', 'description' => 'Computers and tablets'],
                ['name' => 'Gym and Sports Equipment', 'description' => 'Fitness gear'],
                ['name' => 'Internet Subscription', 'description' => 'Broadband and mobile data'],
                ['name' => 'Additional Sports Equipment', 'description' => 'Extra sports gear'],
                ['name' => 'Sports Rental and Entrance Fees', 'description' => 'Facility access'],
                ['name' => 'Sports Competition Fees', 'description' => 'Tournament participation'],
                ['name' => 'Breastfeeding Equipment', 'description' => 'Pumps and accessories'],
                ['name' => 'Childcare Fees', 'description' => 'Daycare and babysitting'],
                ['name' => 'SSPN', 'description' => 'National Education Savings Scheme'],
                ['name' => 'Life Insurance', 'description' => 'Insurance premiums'],
                ['name' => 'EPF', 'description' => 'Employee Provident Fund'],
                ['name' => 'PRS and Deferred Annuity', 'description' => 'Retirement savings'],
                ['name' => 'Education and Medical Insurance', 'description' => 'Insurance coverage'],
                ['name' => 'SOCSO', 'description' => 'Social security contributions'],
                ['name' => 'Electric Vehicle Fee', 'description' => 'EV-related expenses'],
                ['name' => 'Miscellaneous', 'description' => 'Default if no clear category applies'],
            ]);
        }

        // Prepare batch insert data for categories
        $categoryData = $defaultCategories->map(function ($category) use ($userId) {
            return [
                'name' => $category['name'],
                'description' => $category['description'],
                'is_default' => true,
                'is_system' => false,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        // Batch insert categories (more efficient than individual inserts)
        ExpenseCategory::insert($categoryData);

        // Default payment methods
        $paymentMethods = [
            ['name' => 'Cash', 'description' => 'Cash payment'],
            ['name' => 'Credit Card', 'description' => 'Credit card payment'],
            ['name' => 'Debit Card', 'description' => 'Debit card payment'],
            ['name' => 'Bank Transfer', 'description' => 'Direct bank transfer'],
            ['name' => 'Mobile Payment', 'description' => 'Mobile payment apps'],
        ];

        // Prepare batch insert data for payment methods
        $paymentMethodData = collect($paymentMethods)->map(function ($method) use ($userId) {
            return [
                'name' => $method['name'],
                'description' => $method['description'],
                'is_default' => true,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        // Batch insert payment methods
        PaymentMethod::insert($paymentMethodData);
        
        // Add default income categories
        $incomeCategories = [
            ['name' => 'salary', 'color' => '#10B981'], // green-500
            ['name' => 'freelance', 'color' => '#3B82F6'], // blue-500
            ['name' => 'investment', 'color' => '#8B5CF6'], // purple-500
            ['name' => 'rental', 'color' => '#F59E0B'], // yellow-500
            ['name' => 'business', 'color' => '#6366F1'], // indigo-500
            ['name' => 'other', 'color' => '#6B7280'], // gray-500
        ];
        
        // Prepare batch insert data for income categories
        $incomeCategoryData = collect($incomeCategories)->map(function ($category) use ($userId) {
            return [
                'name' => $category['name'],
                'color' => $category['color'],
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
        
        // Batch insert income categories
        UserIncomeCategory::insert($incomeCategoryData);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
