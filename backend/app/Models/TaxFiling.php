<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxFiling extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'period_id',
        'filing_date',
        'total_income',
        'total_expenses',
        'total_deductions',
        'tax_amount',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'filing_date' => 'date',
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the tax filing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tax filing period for the filing.
     */
    public function period()
    {
        return $this->belongsTo(TaxFilingPeriod::class, 'period_id');
    }
}
