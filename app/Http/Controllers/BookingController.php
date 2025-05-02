<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PhotographerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\URL; // Import URL facade for signed URLs
use Carbon\Carbon; // Import Carbon for date handling
use Symfony\Component\HttpFoundation\StreamedResponse; // For file download

class BookingController extends Controller
{
    /**
     * Show the form for creating a new booking for a specific photographer.
     *
     * @param  \App\Models\PhotographerProfile  $photographerProfile
     * @return \Illuminate\View\View
     */
    public function create(PhotographerProfile $photographerProfile)
    {
        // Pass the photographer profile to the view
        return view("bookings.create", compact("photographerProfile"));
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PhotographerProfile  $photographerProfile
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, PhotographerProfile $photographerProfile)
    {
        $validator = Validator::make($request->all(), [
            "client_name" => ["required", "string", "max:255"],
            "client_contact_number" => ["required", "string", "max:20"], // Basic validation
            "client_email" => ["nullable", "email", "max:255"],
            "event_date" => ["required", "date", "after_or_equal:today"],
            "event_location" => ["required", "string", "max:255"],
            "event_details" => ["nullable", "string"],
            "payment_proof" => ["nullable", "file", "mimes:jpg,jpeg,png,pdf", "max:5120"], // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData["photographer_profile_id"] = $photographerProfile->id;
        $validatedData["status"] = "pending"; // Default status

        // Handle payment proof upload
        if ($request->hasFile("payment_proof")) {
            try {
                // Store in a specific directory, e.g., payment_proofs/{photographer_id}/{booking_id}
                // Let's use a unique name to avoid conflicts
                $fileName = time() . "_" . $request->file("payment_proof")->getClientOriginalName();
                $path = $request->file("payment_proof")->storeAs("payment_proofs/" . $photographerProfile->id, $fileName, "public");
                $validatedData["payment_proof_path"] = $path;
            } catch (\Exception $e) {
                // Log the error
                
                return redirect()->back()
                            ->with("error", __("Failed to upload payment proof. Please try again."))
                            ->withInput();
            }
        }

        try {
            $booking = Booking::create($validatedData);

            // Optionally: Send notification to photographer
            // Mail::to($photographerProfile->user->email)->send(new NewBookingRequest($booking));

            return redirect()->route("photographers.show", $photographerProfile)
                         ->with("success", __("Booking request submitted successfully! The photographer will contact you soon."));

        } catch (\Exception $e) {
            // Log the error
            
            // Clean up uploaded file if booking creation failed
            if (isset($validatedData["payment_proof_path"]) && Storage::disk("public")->exists($validatedData["payment_proof_path"])) {
                Storage::disk("public")->delete($validatedData["payment_proof_path"]);
            }

            return redirect()->back()
                        ->with("error", __("An error occurred while submitting your booking. Please try again."))
                        ->withInput();
        }
    }

    /**
     * Display the photographer's booking dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || !$user->photographerProfile) {
            return redirect()->route("photographers.index")->with("error", __("Photographer profile not found or you are not authorized to view this dashboard."));
        }

        $photographerProfileId = $user->photographerProfile->id;
        $now = Carbon::now();
        $today = Carbon::today();
        $endOfToday = Carbon::today()->endOfDay();
        $nextWeek = Carbon::today()->addDays(7)->endOfDay();

        // Fetch all relevant bookings first
        $allBookings = Booking::where("photographer_profile_id", $photographerProfileId)
                           ->orderBy("event_date", "asc")
                           ->get();

        // Filter bookings for different sections
        $todaysBookings = $allBookings->where("event_date", ">=", $today)
                                     ->where("event_date", "<=", $endOfToday)
                                     ->whereNotIn("status", ["rejected", "completed"]);

        $upcomingBookings = $allBookings->where("event_date", ">", $endOfToday)
                                       ->whereNotIn("status", ["rejected", "completed"]);

        $pastOrCompletedBookings = $allBookings->where("event_date", "<", $today)
                                              ->orWhereIn("status", ["rejected", "completed"]);

        return view("bookings.dashboard", compact(
            "todaysBookings", 
            "upcomingBookings", 
            "pastOrCompletedBookings"
        ));
    }

    /**
     * Display the specified booking.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\View\View
     */
    public function show(Booking $booking)
    {
        // Authorization: Ensure the logged-in user is the photographer for this booking
        $user = Auth::user();
        if (!$user || !$user->photographerProfile || $booking->photographer_profile_id !== $user->photographerProfile->id) {
            abort(403, __("Unauthorized action."));
        }

        // Generate temporary download link if file exists
        $downloadUrl = null;
        if ($booking->final_images_link && Storage::disk("public")->exists($booking->final_images_link)) {
            try {
                $downloadUrl = URL::temporarySignedRoute(
                    "bookings.download",
                    now()->addHours(1),
                    ["booking" => $booking->id]
                );
            } catch (\Exception $e) {
                // Log error
                
            }
        }

        return view("bookings.show", compact("booking", "downloadUrl"));
    }

    /**
     * Handle upload of final images for a booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadFinalImages(Request $request, Booking $booking)
    {
        // Authorization: Ensure the logged-in user is the photographer for this booking
        $user = Auth::user();
        if (!$user || !$user->photographerProfile || $booking->photographer_profile_id !== $user->photographerProfile->id) {
            abort(403, __("Unauthorized action."));
        }

        // Validate the uploaded file (e.g., zip, max size)
        $validator = Validator::make($request->all(), [
            // Make final_images optional if only updating deadline/status
            "final_images" => ["nullable", "file", "mimes:zip", "max:102400"], // Example: 100MB max zip file
            "delivery_deadline" => ["nullable", "date"],
            "status" => ["nullable", "in:completed"],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $fileUploaded = false;
        $oldFilePath = $booking->final_images_link;

        if ($request->hasFile("final_images")) {
            try {
                // Delete old file if it exists and is different
                if ($oldFilePath && Storage::disk("public")->exists($oldFilePath)) {
                    Storage::disk("public")->delete($oldFilePath);
                }

                // Store the new file with a unique name
                $fileName = time() . "_final_" . $request->file("final_images")->getClientOriginalName();
                $path = $request->file("final_images")->storeAs("final_images/" . $booking->id, $fileName, "public");
                $booking->final_images_link = $path;
                $fileUploaded = true;

            } catch (\Exception $e) {
                // Log the error
                
                return redirect()->back()
                            ->with("error", __("Failed to upload final images. Please try again."))
                            ->withInput();
            }
        }

        // Update deadline and status if provided
        if (array_key_exists("delivery_deadline", $validatedData)) {
            $booking->delivery_deadline = $validatedData["delivery_deadline"];
        }
        if (isset($validatedData["status"]) && $validatedData["status"] === "completed") {
            $booking->status = "completed";
        }

        try {
            $booking->save();
            // Optionally: Notify client that images are ready
            // if ($fileUploaded) { Mail::to($booking->client_email)->send(new ImagesReadyNotification($booking)); }

            return redirect()->route("bookings.show", $booking)
                         ->with("success", __("Booking details updated successfully."));

        } catch (\Exception $e) {
            // Log the error
            
            // Attempt to clean up newly uploaded file if save failed
            if ($fileUploaded && isset($path) && Storage::disk("public")->exists($path)) {
                Storage::disk("public")->delete($path);
                $booking->final_images_link = $oldFilePath; // Revert in memory if possible
            }

            return redirect()->back()
                        ->with("error", __("An error occurred while saving booking details. Please try again."))
                        ->withInput();
        }
    }

    /**
     * Handle download of final images for a booking via signed URL.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadFinalImages(Booking $booking)
    {
        // Authorization: Ensure the logged-in user is the photographer for this booking
        // The signed URL middleware already verifies the signature and expiry.
        // We still need to ensure the *current* user is authorized.
        $user = Auth::user();
        if (!$user || !$user->photographerProfile || $booking->photographer_profile_id !== $user->photographerProfile->id) {
            // Optionally, allow client download if clients have accounts and are authorized
            abort(403, __("Unauthorized action."));
        }

        // Check if the file path exists and the file is present
        if (!$booking->final_images_link || !Storage::disk("public")->exists($booking->final_images_link)) {
            return redirect()->route("bookings.show", $booking)
                         ->with("error", __("Final images file not found."));
        }

        try {
            // Return the file as a download response
            return Storage::disk("public")->download($booking->final_images_link);
        } catch (\Exception $e) {
            // Log the error
            
            return redirect()->route("bookings.show", $booking)
                         ->with("error", __("Could not download the file. Please try again."));
        }
    }

}

