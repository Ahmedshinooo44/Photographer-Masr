<?php

namespace App\Http\Controllers;

use App\Models\PhotographerProfile;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\View\View; // Import View
use Illuminate\Http\RedirectResponse; // Import RedirectResponse
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Storage; // Import Storage facade

class PhotographerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Fetch users with the role 'photographer' who have a profile
        $photographerProfiles = PhotographerProfile::with("user") // Eager load user data
                                ->whereHas("user", function ($query) {
                                    $query->where("role", "photographer");
                                })
                                ->latest() // Optional: order by latest created
                                ->paginate(10); // Optional: paginate results

        return view("photographers.index", compact("photographerProfiles"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View | RedirectResponse
    {
        // Check if the user already has a profile
        if (Auth::user()->photographerProfile) {
            return redirect()->route("my-profile.edit")->with("info", "You already have a profile. You can edit it here.");
        }
        return view("photographers.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Ensure the user is a photographer and doesn't have a profile yet
        if (Auth::user()->role !== "photographer" || Auth::user()->photographerProfile) {
            abort(403); // Or redirect with an error
        }

        $validated = $request->validate([
            "bio" => ["nullable", "string"],
            "contact_phone" => ["nullable", "string", "max:20"],
            "location" => ["nullable", "string", "max:255"],
            "profile_picture" => ["nullable", "image", "max:2048"], // 2MB Max
        ]);

        $profileData = $validated;
        $profileData["user_id"] = Auth::id();

        if ($request->hasFile("profile_picture")) {
            $path = $request->file("profile_picture")->store("profile_pictures", "public");
            $profileData["profile_picture_path"] = $path;
        }

        PhotographerProfile::create($profileData);

        return redirect()->route("dashboard")->with("success", "Profile created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(PhotographerProfile $photographerProfile): View
    {
        // Eager load user and photos
        $photographerProfile->load(["user", "photos"]);
        return view("photographers.show", compact("photographerProfile"));
    }

    /**
     * Show the form for editing the specified resource.
     * For MVP, we assume this edits the logged-in user's profile.
     */
    public function edit(): View | RedirectResponse
    {
        $profile = Auth::user()->photographerProfile;
        if (!$profile) {
            return redirect()->route("my-profile.create")->with("info", "You need to create a profile first.");
        }
        return view("photographers.edit", compact("profile"));
    }

    /**
     * Update the specified resource in storage.
     * For MVP, we assume this updates the logged-in user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $profile = Auth::user()->photographerProfile;
        if (!$profile) {
            abort(404); // Or redirect appropriately
        }

        $validated = $request->validate([
            "bio" => ["nullable", "string"],
            "contact_phone" => ["nullable", "string", "max:20"],
            "location" => ["nullable", "string", "max:255"],
            "profile_picture" => ["nullable", "image", "max:2048"], // 2MB Max
        ]);

        $profileData = $validated;

        if ($request->hasFile("profile_picture")) {
            // Delete old picture if it exists
            if ($profile->profile_picture_path) {
                Storage::disk("public")->delete($profile->profile_picture_path);
            }
            $path = $request->file("profile_picture")->store("profile_pictures", "public");
            $profileData["profile_picture_path"] = $path;
        }

        $profile->update($profileData);

        return redirect()->route("my-profile.edit")->with("success", "Profile updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implement if needed, maybe for admin later
        abort(501); // Not Implemented
    }
}

