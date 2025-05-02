<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("bookings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("photographer_profile_id")->constrained("photographer_profiles")->onDelete("cascade");
            $table->string("client_name");
            $table->string("client_contact_number");
            $table->string("client_email")->nullable();
            $table->dateTime("event_date");
            $table->string("event_location");
            $table->text("event_details")->nullable();
            $table->string("payment_proof_path")->nullable(); // Path to stored file
            $table->enum("status", ["pending", "confirmed", "rejected", "completed"])->default("pending");
            $table->dateTime("delivery_deadline")->nullable();
            $table->string("final_images_link")->nullable(); // Could be a signed URL or path
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("bookings");
    }
};

