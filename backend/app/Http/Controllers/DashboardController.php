<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\DeductibilityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with deductibility summary.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get deductibility types
        $deductibilityTypes = DeductibilityType::all();
        
        // Initialize summary array with all deductibility types
        $deductibilitySummary = [];
        foreach ($deductibilityTypes as $type) {
            $deductibilitySummary[$type->id] = [
                'name' => $type->name,
                'total' => 0,
                'count' => 0,
                'percentage' => 0,
            ];
        }
        
        // Get receipt items with deductibility information for the authenticated user
        $receiptItems = ReceiptItem::select(
                'receipt_items.deductibility_id',
                'deductibility_types.name as deductibility_name',
                DB::raw('SUM(receipt_items.total_price) as total_amount'),
                DB::raw('COUNT(receipt_items.id) as item_count')
            )
            ->join('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
            ->leftJoin('deductibility_types', 'deductibility_types.id', '=', 'receipt_items.deductibility_id')
            ->where('receipts.user_id', Auth::id())
            ->groupBy('receipt_items.deductibility_id', 'deductibility_types.name')
            ->get();
        
        // Calculate total amount for all items
        $totalAmount = $receiptItems->sum('total_amount');
        
        // Fill the summary with actual data
        foreach ($receiptItems as $item) {
            if ($item->deductibility_id) {
                $deductibilitySummary[$item->deductibility_id]['total'] = $item->total_amount;
                $deductibilitySummary[$item->deductibility_id]['count'] = $item->item_count;
                
                // Calculate percentage of total
                if ($totalAmount > 0) {
                    $deductibilitySummary[$item->deductibility_id]['percentage'] = 
                        round(($item->total_amount / $totalAmount) * 100, 2);
                }
            }
        }
        
        // Get recent receipts for the dashboard
        $recentReceipts = Receipt::where('user_id', Auth::id())
            ->with(['vendor', 'paymentMethod'])
            ->orderBy('receipt_date', 'desc')
            ->limit(5)
            ->get();
        
        // Get monthly spending data for chart
        $monthlySpending = $this->getMonthlySpending();
        
        // Get AI-powered tax suggestions
        $taxSuggestions = $this->getTaxSuggestions();
        
        return view('dashboard', compact(
            'deductibilitySummary', 
            'totalAmount', 
            'recentReceipts', 
            'monthlySpending',
            'taxSuggestions'
        ));
    }
    
    /**
     * Get monthly spending data for the last 12 months.
     *
     * @return array
     */
    private function getMonthlySpending()
    {
        $months = [];
        $data = [];
        
        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $months[] = $monthYear;
            
            $monthStart = $date->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->endOfMonth()->format('Y-m-d');
            
            // Get total spending for this month
            $total = Receipt::where('user_id', Auth::id())
                ->whereBetween('receipt_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
                
            $data[] = round($total, 2);
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }
    
    /**
     * Get AI-powered tax suggestions based on user's spending patterns.
     *
     * @return array
     */

     
  /**
 * Get AI-powered tax suggestions based on user's spending patterns.
 *
 * @return array
 */
private function getTaxSuggestions()
{
    try {
        // Call the external API
        $apiBaseUrl = 'http://47.250.52.250:8000';
        $response = Http::timeout(10)->get($apiBaseUrl . '/tax-suggestions');
        
        if ($response->successful()) {
            \Log::info('Tax suggestions API response received', [
                'status' => $response->status(),
                'data' => $response->json()
            ]);
            
            return $response->json();
        }
        
        \Log::warning('Tax suggestions API returned non-success status', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);
        
        // Fallback to default suggestions if API call fails
    } catch (\Exception $e) {
        \Log::error('Error calling tax suggestions API', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Fallback to default suggestions on exception
    }
}


    
    /**
     * Get deductibility summary data for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeductibilitySummary()
    {
        try {
            // Define the deductibility types we want to track
            $deductibilityTypes = [
                'Fully Deductible',
                'Partially Deductible',
                'Non-Deductible'
            ];
            
            // Get receipt items with deductibility information for the authenticated user
            $deductibilitySummary = ReceiptItem::select(
                    'deductibility_types.name as deductibility_name',
                    DB::raw('SUM(receipt_items.total_price) as total_amount'),
                    DB::raw('COUNT(receipt_items.id) as item_count')
                )
                ->join('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
                ->leftJoin('deductibility_types', 'deductibility_types.id', '=', 'receipt_items.deductibility_id')
                ->where('receipts.user_id', Auth::id())
                ->groupBy('receipt_items.deductibility_id', 'deductibility_types.name')
                ->get();
            
            // Calculate total amount for all items
            $totalAmount = $deductibilitySummary->sum('total_amount');
            
            // Initialize result array with zero values for all types
            $result = [];
            foreach ($deductibilityTypes as $type) {
                $result[$type] = [
                    'deductibility_name' => $type,
                    'total_amount' => 0,
                    'item_count' => 0,
                    'percentage' => 0
                ];
            }
            
            // Fill in actual data where available
            foreach ($deductibilitySummary as $item) {
                if ($item->deductibility_name && in_array($item->deductibility_name, $deductibilityTypes)) {
                    $result[$item->deductibility_name] = [
                        'deductibility_name' => $item->deductibility_name,
                        'total_amount' => (float)$item->total_amount,
                        'item_count' => (int)$item->item_count,
                        'percentage' => $totalAmount > 0 
                            ? round(($item->total_amount / $totalAmount) * 100, 2) 
                            : 0
                    ];
                }
            }
            
            return response()->json([
                'summary' => array_values($result),
                'total' => $totalAmount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDeductibilitySummary: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to retrieve deductibility summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI-powered tax suggestions for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaxSuggestionsApi()
    {
        try {
            return response()->json($this->getTaxSuggestions());
        } catch (\Exception $e) {
            \Log::error('Error in getTaxSuggestionsApi: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to retrieve tax suggestions',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
