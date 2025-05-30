<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'contact_info',
        'tax_registration_number',
    ];

    /**
     * Get the receipts for the vendor.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
