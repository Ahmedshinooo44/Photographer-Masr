<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Edit Your Photographer Profile") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session("success"))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-300 rounded-md p-3">
                            {{ session("success") }}
                        </div>
                    @endif
                    @if (session("info"))
                        <div class="mb-4 font-medium text-sm text-blue-600 bg-blue-100 border border-blue-300 rounded-md p-3">
                            {{ session("info") }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route("my-profile.update") }}" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")

                        <!-- Bio -->
                        <div class="mt-4">
                            <x-input-label for="bio" :value="__("Bio / About Me")" />
                            <textarea id="bio" name="bio" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old("bio", $profile->bio) }}</textarea>
                            <x-input-error :messages="$errors->get("bio")" class="mt-2" />
                        </div>

                        <!-- Contact Phone -->
                        <div class="mt-4">
                            <x-input-label for="contact_phone" :value="__("Contact Phone (Optional)")" />
                            <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old("contact_phone", $profile->contact_phone)" autocomplete="tel" />
                            <x-input-error :messages="$errors->get("contact_phone")" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="mt-4">
                            <x-input-label for="location" :value="__("Location (e.g., City, Area)")" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old("location", $profile->location)" autocomplete="address-level2" />
                            <x-input-error :messages="$errors->get("location")" class="mt-2" />
                        </div>

                        <!-- Profile Picture -->
                        <div class="mt-4">
                            <x-input-label for="profile_picture" :value="__("Profile Picture (Optional)")" />
                            @if ($profile->profile_picture_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($profile->profile_picture_path) }}" alt="Current Profile Picture" class="w-32 h-32 object-cover rounded-md shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">{{ __("Current picture. Upload a new one to replace it.") }}</p>
                                </div>
                            @endif
                            <input id="profile_picture" name="profile_picture" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                            <x-input-error :messages="$errors->get("profile_picture")" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __("Update Profile") }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- Section for Photo Uploads --}}
                    <hr class="my-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __("Manage Portfolio Photos") }}</h3>
                    
                    {{-- Display existing photos --}}
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 mb-2">{{ __("Current Photos") }}</h4>
                        @if ($profile->photos->count() > 0)
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
                                @foreach ($profile->photos as $photo)
                                    <div class="relative">
                                        <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->caption ?? "Portfolio image" }}" class="w-full h-auto object-cover rounded-md shadow-md aspect-square">
                                        {{-- Add delete button later if needed --}}
                                        {{-- <form action="{{ route("photos.destroy", $photo) }}" method="POST" class="absolute top-1 right-1">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="p-1 bg-red-600 rounded-full text-white text-xs" onclick="return confirm("Are you sure?")">X</button>
                                        </form> --}}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">{{ __("No portfolio photos uploaded yet.") }}</p>
                        @endif
                    </div>

                    {{-- Photo Upload Form --}}
                    <form method="POST" action="{{ route("photos.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mt-4">
                            <x-input-label for="photos" :value="__("Upload New Photos (Select multiple)")" />
                            <input id="photos" name="photos[]" type="file" multiple class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                            <x-input-error :messages="$errors->get("photos")" class="mt-2" />
                            <x-input-error :messages="$errors->get("photos.*")" class="mt-2" /> {{-- Show errors for individual files --}}
                        </div>
                        {{-- Optional: Add caption field per photo later --}}
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __("Upload Photos") }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

