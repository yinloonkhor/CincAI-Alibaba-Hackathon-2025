<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Add this import

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Add HasApiTokens trait here

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nric',
        'phone_number',
        'tin',
        'age', 
        'data_filled', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'data_filled' => 'boolean', 
    ];

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    /**
     * Get the payment methods for the user.
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Get the receipts for the user.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Get the tax filing periods for the user.
     */
    public function taxFilingPeriods()
    {
        return $this->hasMany(TaxFilingPeriod::class);
    }

    /**
     * Get the tax filings for the user.
     */
    public function taxFilings()
    {
        return $this->hasMany(TaxFiling::class);
    }

    public function incomes()
{
    return $this->hasMany(Income::class);
}

/**
 * Get the income categories for the user.
 */
public function incomeCategories()
{
    return $this->hasMany(UserIncomeCategory::class);
}

public function chatbotLogs()
{
    return $this->hasMany(ChatbotLog::class);
}
}
