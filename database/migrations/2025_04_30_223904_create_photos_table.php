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
        Schema::create("photos", function (Blueprint $table) {
            $table->id();
            $table->foreignId("photographer_profile_id")->constrained()->onDelete("cascade"); // Link to photographer_profiles table
            $table->string("image_path"); // Path to the stored image file
            $table->string("caption")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
