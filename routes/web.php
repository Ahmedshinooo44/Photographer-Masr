<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhotographerProfileController; // Added this line
use App\Http\Controllers\PhotoController; // Added this line
use App\Http\Controllers\BookingController; // Added for booking routes
use App\Http\Controllers\MessageController; // Added for messaging routes
// use App\Http\Controllers\FinalDeliveryController; // Removed, using BookingController
use App\Http\Controllers\LocaleController; // Added for language switching
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", function () {
    // Maybe redirect to photographers list or a landing page?
    // For now, keep the welcome view.
    return view("welcome");
});

// Public routes for viewing photographers
Route::get("/photographers", [PhotographerProfileController::class, "index"])->name("photographers.index");
Route::get("/photographers/{photographerProfile}", [PhotographerProfileController::class, "show"])->name("photographers.show");

// Public routes for booking a photographer
Route::get("/photographers/{photographerProfile}/book", [BookingController::class, "create"])->name("bookings.create");
Route::post("/photographers/{photographerProfile}/book", [BookingController::class, "store"])->name("bookings.store");


// Photographer Dashboard Route (replaces generic dashboard)
Route::get("/dashboard", [BookingController::class, "dashboard"])
    ->middleware(["auth", "verified"]) // Ensure user is logged in and verified
    ->name("dashboard"); // Keep the name "dashboard"

Route::middleware("auth")->group(function () {
    // Standard user profile routes from Breeze
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");

    // Photographer-specific profile management routes
    // We might need middleware here to check if user->role === "photographer"
    Route::get("/my-profile/create", [PhotographerProfileController::class, "create"])->name("my-profile.create");
    Route::post("/my-profile", [PhotographerProfileController::class, "store"])->name("my-profile.store");
    Route::get("/my-profile/edit", [PhotographerProfileController::class, "edit"])->name("my-profile.edit"); // Assumes edit shows form for logged-in user"s profile
    Route::put("/my-profile", [PhotographerProfileController::class, "update"])->name("my-profile.update"); // Assumes update handles logged-in user"s profile

    // Photo Upload Route
    Route::post("/photos", [PhotoController::class, "store"])->name("photos.store");
    // Route::delete("/photos/{photo}", [PhotoController::class, "destroy"])->name("photos.destroy"); // Add later if needed

    // Messaging Routes (Protected)
    Route::get("/messages/{conversationId}", [MessageController::class, "index"])->name("messages.show");
    Route::post("/messages", [MessageController::class, "store"])->name("messages.store");
    // Add route for listing conversations later (e.g., /messages)

    // Add route for listing conversations later (e.g., /messages)

    // Booking Details & File Upload Routes (Protected for photographer)
    Route::get("/bookings/{booking}", [BookingController::class, "show"])->name("bookings.show");
    Route::patch("/bookings/{booking}/upload", [BookingController::class, "uploadFinalImages"])->name("bookings.upload");
});

// Public Download Route (Signed URL)
Route::get("/bookings/{booking}/download", function (Request $request, Booking $booking) {
    // This closure ensures the signed URL middleware runs before hitting the controller
    // Now call the actual download handler method in the controller
    if (! $request->hasValidSignature()) {
        abort(401);
    }

    // Authorization: Check if the file exists and belongs to the booking
    if (!$booking->final_images_link || !Storage::disk("public")->exists($booking->final_images_link)) {
        abort(404, __("File not found or has been removed."));
    }

    // Return the file for download
    return Storage::disk("public")->download($booking->final_images_link);

})->name("bookings.download"); // Keep this name for URL generation

// Language Switcher Route
Route::get("language/{locale}", [LocaleController::class, "switchLocale"])->name("language.switch");

require __DIR__."/auth.php";



    // Route to start a conversation with a photographer
    Route::get("/messages/start/{photographerProfile}", [MessageController::class, "startConversation"])->name("messages.start");



    // Route for downloading final images (signed URL)
    Route::get("/bookings/{booking}/download", [BookingController::class, "downloadFinalImages"])->name("bookings.download")->middleware("signed");

