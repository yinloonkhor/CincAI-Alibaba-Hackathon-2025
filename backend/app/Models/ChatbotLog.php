<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_message',
        'bot_response',
     
    ];
    /**
     * Get the user that owns the chatbot log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
