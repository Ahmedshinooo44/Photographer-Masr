<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "photographer_profile_id",
        "client_name",
        "client_contact_number",
        "client_email",
        "event_date",
        "event_location",
        "event_details",
        "payment_proof_path",
        "status",
        "delivery_deadline", // Keep if added previously
        "final_files_path", // Added
        "download_token",   // Added
        "token_expires_at", // Added
        // Remove "final_images_link" if it's replaced by the new system
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "event_date" => "datetime",
        "delivery_deadline" => "datetime",
    ];

    /**
     * Get the photographer profile that owns the booking.
     */
    public function photographerProfile(): BelongsTo
    {
        return $this->belongsTo(PhotographerProfile::class);
    }

    // Optional: Add relationship to a User model if clients can register
    // public function clientUser(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, "client_user_id"); // Assuming a client_user_id column
    // }
}

