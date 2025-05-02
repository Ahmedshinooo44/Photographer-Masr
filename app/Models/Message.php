<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "sender_user_id",
        "recipient_user_id",
        "guest_identifier",
        "conversation_id",
        "content",
        "read_at",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "read_at" => "datetime",
    ];

    /**
     * Get the user who sent the message.
     */
    public function sender(): BelongsTo
    {
        // Can be null if sender is a guest
        return $this->belongsTo(User::class, "sender_user_id");
    }

    /**
     * Get the user who received the message (the photographer).
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, "recipient_user_id");
    }
}

