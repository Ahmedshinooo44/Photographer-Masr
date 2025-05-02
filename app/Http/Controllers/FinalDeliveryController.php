<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class FinalDeliveryController extends Controller
{
    /**
     * Show the form for uploading final files for a booking.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\View\View | \Illuminate\Http\RedirectResponse
     */
    public function showUploadForm(Booking $booking)
    {
        // Ensure the logged-in user is the photographer for this booking
        if (Auth::id() !== $booking->photographerProfile->user_id) {
            abort(403, __("Unauthorized action."));
        }

        // Check if booking status allows upload (e.g., confirmed, processing)
        // if (!in_array($booking->status, ["confirmed", "processing"])) {
        //     return redirect()->route("dashboard")->with("error", __("Cannot upload files for this booking status."));
        // }

        return view("deliveries.upload", compact("booking"));
    }

    /**
     * Handle the upload of final files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleUpload(Request $request, Booking $booking)
    {
        // Ensure the logged-in user is the photographer for this booking
        if (Auth::id() !== $booking->photographerProfile->user_id) {
            abort(403, __("Unauthorized action."));
        }

        $validator = Validator::make($request->all(), [
            // Allow multiple files or a single zip file
            "final_files" => ["required", "array", "min:1"], // Require an array of files
            "final_files.*" => ["file", "mimes:jpg,jpeg,png,zip", "max:102400"], // 100MB max per file (adjust as needed)
            // Or for single zip:
            // "final_zip" => ["required", "file", "mimes:zip", "max:512000"], // 500MB max zip
            "delivery_deadline" => ["nullable", "date", "after_or_equal:today"], // Allow updating deadline
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $files = $request->file("final_files");
        $storagePath = "final_deliveries/booking_" . $booking->id;

        // Clear existing files if re-uploading?
        if ($booking->final_files_path && Storage::disk("public")->exists($booking->final_files_path)) {
            Storage::disk("public")->deleteDirectory($booking->final_files_path);
        }

        $uploadedPaths = [];
        try {
            foreach ($files as $file) {
                $path = $file->store($storagePath, "public");
                $uploadedPaths[] = $path; // Store individual paths or just the directory?
            }

            // Generate secure download token
            $token = Str::random(40);
            $expiresAt = now()->addDays(14); // Link expires in 14 days (configurable)

            // Update booking record
            $booking->final_files_path = $storagePath; // Store the directory path
            $booking->download_token = $token;
            $booking->token_expires_at = $expiresAt;
            $booking->status = "delivered"; // Update status
            if (isset($validatedData["delivery_deadline"])) {
                $booking->delivery_deadline = $validatedData["delivery_deadline"];
            }
            $booking->save();

            // Generate the download link
            $downloadLink = URL::temporarySignedRoute(
                "deliveries.download",
                $expiresAt,
                ["token" => $token]
            );

            // Optionally: Notify client via email with the download link
            // Mail::to($booking->client_email)->send(new FilesReadyForDownload($booking, $downloadLink));

            return redirect()->route("dashboard")
                         ->with("success", __("Final files uploaded successfully! Download link generated.") . " " . $downloadLink);

        } catch (\Exception $e) {
            // Log the error
            
            // Clean up partially uploaded files
            Storage::disk("public")->deleteDirectory($storagePath);

            return redirect()->back()
                        ->with("error", __("An error occurred during file upload. Please try again.") . " Error: " . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Handle the download of final files using a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Symfony\Component\HttpFoundation\StreamedResponse | \Illuminate\Http\RedirectResponse
     */
    public function handleDownload(Request $request, string $token)
    {
        if (!$request->hasValidSignature()) {
             return view("deliveries.invalid_link", ["message" => __("Download link is invalid or expired.")]);
        }

        $booking = Booking::where("download_token", $token)
                          ->where("token_expires_at", ">", now())
                          ->first();

        if (!$booking || !$booking->final_files_path) {
            return view("deliveries.invalid_link", ["message" => __("Download link is invalid, expired, or files are not available.")]);
        }

        $directoryPath = $booking->final_files_path;
        $files = Storage::disk("public")->files($directoryPath);

        if (empty($files)) {
             return view("deliveries.invalid_link", ["message" => __("No files found for this download link.")]);
        }

        // If multiple files, zip them on the fly
        if (count($files) > 1) {
            $zipFileName = "booking_" . $booking->id . "_files.zip";
            $zipPath = storage_path("app/temp/" . $zipFileName); // Temp storage for zip
            
            // Ensure temp directory exists
            if (!Storage::exists("temp")) {
                Storage::makeDirectory("temp");
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($files as $filePath) {
                    $fileName = basename($filePath);
                    $absolutePath = Storage::disk("public")->path($filePath);
                    $zip->addFile($absolutePath, $fileName);
                }
                $zip->close();

                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                 return view("deliveries.invalid_link", ["message" => __("Could not create zip file for download.")]);
            }
        } elseif (count($files) === 1) {
            // If single file, download directly
            $filePath = $files[0];
            $fileName = basename($filePath);
            return Storage::disk("public")->download($filePath, $fileName);
        } else {
             return view("deliveries.invalid_link", ["message" => __("No files found for this download link.")]);
        }
    }
}

