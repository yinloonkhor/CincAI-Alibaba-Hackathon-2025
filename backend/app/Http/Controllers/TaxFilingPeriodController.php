<?php

namespace App\Http\Controllers;

use App\Models\TaxFilingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaxFilingPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periods = TaxFilingPeriod::where('user_id', Auth::id())
            ->orderBy('end_date', 'desc')
            ->paginate(15);
        
        return view('tax-filing-periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tax-filing-periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:Open,Closed',
        ]);

        $validated['user_id'] = Auth::id();

        $period = TaxFilingPeriod::create($validated);

        return redirect()->route('tax-filing-periods.index')
            ->with('success', 'Tax filing period created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxFilingPeriod $taxFilingPeriod)
    {
        $this->authorize('view', $taxFilingPeriod);
        
        $taxFilingPeriod->load('taxFilings');
        
        return view('tax-filing-periods.show', compact('taxFilingPeriod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxFilingPeriod $taxFilingPeriod)
    {
        $this->authorize('update', $taxFilingPeriod);
        
        return view('tax-filing-periods.edit', compact('taxFilingPeriod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxFilingPeriod $taxFilingPeriod)
    {
        $this->authorize('update', $taxFilingPeriod);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:Open,Closed',
        ]);

        $taxFilingPeriod->update($validated);

        return redirect()->route('tax-filing-periods.index')
            ->with('success', 'Tax filing period updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxFilingPeriod $taxFilingPeriod)
    {
        $this->authorize('delete', $taxFilingPeriod);
        
        // Check if this period has any tax filings
        if ($taxFilingPeriod->taxFilings()->count() > 0) {
            return redirect()->route('tax-filing-periods.index')
                ->with('error', 'Cannot delete period because it has tax filings.');
        }
        
        $taxFilingPeriod->delete();

        return redirect()->route('tax-filing-periods.index')
            ->with('success', 'Tax filing period deleted successfully.');
    }

    /**
     * Create a default tax filing period for the current year.
     */
    public function createDefault()
    {
        $currentYear = Carbon::now()->year;
        $startDate = Carbon::createFromDate($currentYear, 1, 1);
        $endDate = Carbon::createFromDate($currentYear, 12, 31);
        
        $period = TaxFilingPeriod::create([
            'user_id' => Auth::id(),
            'name' => "Tax Year $currentYear",
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'Open',
        ]);
        
        return redirect()->route('tax-filing-periods.index')
            ->with('success', "Default tax filing period for $currentYear created successfully.");
    }
}
