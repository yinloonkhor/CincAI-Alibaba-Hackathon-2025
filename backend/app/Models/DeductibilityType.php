<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductibilityType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the receipt items for the deductibility type.
     */
    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class, 'deductibility_id');
    }
}
