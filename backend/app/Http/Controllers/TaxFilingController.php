<?php

namespace App\Http\Controllers;

use App\Models\TaxFilingPeriod;
use App\Models\TaxFiling;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaxFilingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxFilings = TaxFiling::where('user_id', Auth::id())
            ->with('period')
            ->orderBy('filing_date', 'desc')
            ->paginate(15);
        
        return view('tax-filings.index', compact('taxFilings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periods = TaxFilingPeriod::where('user_id', Auth::id())
            ->orderBy('end_date', 'desc')
            ->get();
        
        return view('tax-filings.create', compact('periods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:tax_filing_periods,id',
            'filing_date' => 'nullable|date',
            'total_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $period = TaxFilingPeriod::findOrFail($validated['period_id']);
        
        // Calculate total expenses and deductions
        $receipts = Receipt::where('user_id', Auth::id())
            ->whereBetween('receipt_date', [$period->start_date, $period->end_date])
            ->with(['items.deductibilityType'])
            ->get();
        
        $totalExpenses = $receipts->sum('total_amount');
        
        $totalDeductions = 0;
        foreach ($receipts as $receipt) {
            foreach ($receipt->items as $item) {
                if ($item->deductibilityType && $item->deductibilityType->name !== 'Non-Deductible') {
                    $deductionAmount = $item->total_price * ($item->deduction_percentage / 100);
                    $totalDeductions += $deductionAmount;
                }
            }
        }
        
        // Create tax filing
        $taxFiling = new TaxFiling([
            'user_id' => Auth::id(),
            'period_id' => $validated['period_id'],
            'filing_date' => $validated['filing_date'] ?? Carbon::now(),
            'total_income' => $validated['total_income'] ?? 0,
            'total_expenses' => $totalExpenses,
            'total_deductions' => $totalDeductions,
            'tax_amount' => 0, // This would be calculated based on tax rules
            'status' => 'Draft',
            'notes' => $validated['notes'],
        ]);
        
        $taxFiling->save();
        
        return redirect()->route('tax-filings.show', $taxFiling)
            ->with('success', 'Tax filing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxFiling $taxFiling)
    {
        $this->authorize('view', $taxFiling);
        
        $taxFiling->load('period');
        
        // Get receipts for this period
        $receipts = Receipt::where('user_id', Auth::id())
            ->whereBetween('receipt_date', [$taxFiling->period->start_date, $taxFiling->period->end_date])
            ->with(['vendor', 'items.category', 'items.deductibilityType'])
            ->get();
        
        return view('tax-filings.show', compact('taxFiling', 'receipts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxFiling $taxFiling)
    {
        $this->authorize('update', $taxFiling);
        
        $periods = TaxFilingPeriod::where('user_id', Auth::id())
            ->orderBy('end_date', 'desc')
            ->get();
        
        return view('tax-filings.edit', compact('taxFiling', 'periods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxFiling $taxFiling)
    {
        $this->authorize('update', $taxFiling);
        
        $validated = $request->validate([
            'period_id' => 'required|exists:tax_filing_periods,id',
            'filing_date' => 'nullable|date',
            'total_income' => 'nullable|numeric|min:0',
            'total_expenses' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Submitted,Accepted,Rejected',
            'notes' => 'nullable|string',
        ]);

        $taxFiling->update($validated);

        return redirect()->route('tax-filings.show', $taxFiling)
            ->with('success', 'Tax filing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxFiling $taxFiling)
    {
        $this->authorize('delete', $taxFiling);
        
        $taxFiling->delete();

        return redirect()->route('tax-filings.index')
            ->with('success', 'Tax filing deleted successfully.');
    }

    /**
     * Submit the tax filing.
     */
    public function submit(TaxFiling $taxFiling)
    {
        $this->authorize('update', $taxFiling);
        
        if ($taxFiling->status === 'Draft') {
            $taxFiling->update([
                'status' => 'Submitted',
                'filing_date' => Carbon::now(),
            ]);
            
            return redirect()->route('tax-filings.show', $taxFiling)
                ->with('success', 'Tax filing submitted successfully.');
        }
        
        return redirect()->route('tax-filings.show', $taxFiling)
            ->with('error', 'Tax filing cannot be submitted because it is not in Draft status.');
    }
}
