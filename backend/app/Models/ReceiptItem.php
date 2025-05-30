<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'category_id',
        'deductibility_id',
        'deduction_percentage',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'deduction_percentage' => 'decimal:2',
    ];

    /**
     * Get the receipt that owns the item.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * Get the expense category for the item.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the deductibility type for the item.
     */
    public function deductibilityType()
    {
        return $this->belongsTo(DeductibilityType::class, 'deductibility_id');
    }
}
