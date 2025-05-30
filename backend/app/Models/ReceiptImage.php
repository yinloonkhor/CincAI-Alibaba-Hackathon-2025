<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_id',
        'image_path',
        'original_filename',
        'mime_type',
        'file_size',
        'extracted_text',
    ];

    /**
     * Get the receipt that owns the image.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
