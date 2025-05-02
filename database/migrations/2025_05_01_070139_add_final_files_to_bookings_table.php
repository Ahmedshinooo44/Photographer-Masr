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
        Schema::table("bookings", function (Blueprint $table) {
            // Path to the stored final files (e.g., a zip archive or a directory path)
            $table->string("final_files_path")->nullable()->after("payment_proof_path");
            // Secure token for client download link
            $table->string("download_token")->unique()->nullable()->after("final_files_path");
            // Expiry date/time for the download token
            $table->timestamp("token_expires_at")->nullable()->after("download_token");
            // Add delivery_deadline column if not already present (from dashboard implementation)
            if (!Schema::hasColumn("bookings", "delivery_deadline")) {
                 $table->date("delivery_deadline")->nullable()->after("event_details");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("bookings", function (Blueprint $table) {
            $table->dropColumn(["final_files_path", "download_token", "token_expires_at"]);
            // Optionally drop delivery_deadline if it was added here
            // if (Schema::hasColumn("bookings", "delivery_deadline")) {
            //     $table->dropColumn("delivery_deadline");
            // }
        });
    }
};
