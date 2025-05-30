<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_default',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get the user that owns the expense category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the receipt items for the expense category.
     */
    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class, 'category_id');
    }
}
