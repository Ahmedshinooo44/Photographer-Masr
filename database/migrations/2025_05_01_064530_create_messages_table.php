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
        Schema::create("messages", function (Blueprint $table) {
            $table->id();
            // Use nullable foreign keys for sender (guest) and recipient (photographer)
            $table->foreignId("sender_user_id")->nullable()->constrained("users")->onDelete("set null");
            $table->foreignId("recipient_user_id")->constrained("users")->onDelete("cascade"); // Photographer must exist
            
            // Identifier for guest conversations (e.g., session ID or unique token)
            $table->string("guest_identifier")->nullable()->index(); 
            
            // A way to group messages belonging to the same conversation
            // Could be generated like "photographer_{id}_guest_{guest_id}" or "user_{id1}_user_{id2}"
            $table->string("conversation_id")->index(); 
            
            $table->text("content");
            $table->timestamp("read_at")->nullable();
            $table->timestamps(); // created_at, updated_at

            // Add index for faster querying of conversations
            $table->index(["sender_user_id", "recipient_user_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("messages");
    }
};

