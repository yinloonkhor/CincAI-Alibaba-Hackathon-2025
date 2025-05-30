<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptImageController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\UserIncomeCategoryController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserPreferenceController::class, 'getUser']);
    
    Route::prefix('receipts')->group(function () {
        Route::post('/images/process-ai', [ReceiptImageController::class, 'processWithAi']);
        Route::post('/', [ReceiptController::class, 'storeFromJson']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/deductibility-summary', [DashboardController::class, 'getDeductibilitySummary']);
        Route::get('/tax-suggestions', [DashboardController::class, 'getTaxSuggestionsApi']);

    });
    Route::get('/incomes/summary-report', [IncomeController::class, 'generateSummaryReport']);

    Route::get('/incomes/statistics', [IncomeController::class, 'statistics']);
    Route::apiResource('incomes', IncomeController::class);
    
    // Income category routes
    Route::get('/income-categories/defaults', [UserIncomeCategoryController::class, 'getDefaultCategories']);
    Route::apiResource('income-categories', UserIncomeCategoryController::class);

    Route::get('/expense-categories', [UserPreferenceController::class, 'getExpenseCategories']);
    Route::post('/expense-categories', [UserPreferenceController::class, 'storeExpenseCategory']);
    Route::post('/expenses', [UserPreferenceController::class, 'storeExpenses']);
    Route::get('/get-recent-expenses', [UserPreferenceController::class, 'getRecentExpenses']);
    Route::get('/get-expenses-summary', [UserPreferenceController::class, 'getExpensesSummary']);
    Route::get('/get-income-summary', [IncomeController::class, 'getIncomeSummary']);

    Route::post('/user-preference', [UserPreferenceController::class, 'store']);
    Route::get('/user-preference', [UserPreferenceController::class, 'getUserPreferences']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Chatbot routes
    Route::post('/chat', [App\Http\Controllers\ChatbotController::class, 'chat']);
    Route::get('/chat/history', [App\Http\Controllers\ChatbotController::class, 'history']);
    Route::delete('/chat/history', [App\Http\Controllers\ChatbotController::class, 'clearHistory']);

});