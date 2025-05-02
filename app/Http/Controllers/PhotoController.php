<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * Store newly uploaded photos in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = Auth::user()->photographerProfile;

        // Ensure the user is a photographer and has a profile
        if (Auth::user()->role !== "photographer" || !$profile) {
            abort(403); // Or redirect with an error
        }

        $request->validate([
            "photos" => ["required", "array"], // Ensure photos is an array
            "photos.*" => ["required", "image", "max:5120"], // 5MB Max per photo
            // Add validation for captions if implemented
        ]);

        if ($request->hasFile("photos")) {
            foreach ($request->file("photos") as $file) {
                $path = $file->store("portfolio_photos", "public");
                
                // Create photo record in database
                Photo::create([
                    "photographer_profile_id" => $profile->id,
                    "image_path" => $path,
                    // "caption" => $request->input("captions")[$index] ?? null, // Add if captions are implemented per photo
                ]);
            }
        }

        return redirect()->route("my-profile.edit")->with("success", "Photos uploaded successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Photo $photo): RedirectResponse
    // {
    //     // Add authorization check: ensure the logged-in user owns this photo
    //     if (Auth::user()->photographerProfile?->id !== $photo->photographer_profile_id) {
    //         abort(403);
    //     }

    //     // Delete file from storage
    //     Storage::disk("public")->delete($photo->image_path);

    //     // Delete record from database
    //     $photo->delete();

    //     return redirect()->route("my-profile.edit")->with("success", "Photo deleted successfully!");
    // }
}

