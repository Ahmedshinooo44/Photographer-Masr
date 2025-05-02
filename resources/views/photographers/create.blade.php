<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Create Your Photographer Profile") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session("info"))
                        <div class="mb-4 font-medium text-sm text-blue-600 bg-blue-100 border border-blue-300 rounded-md p-3">
                            {{ session("info") }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route("my-profile.store") }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Bio -->
                        <div class="mt-4">
                            <x-input-label for="bio" :value="__("Bio / About Me")" />
                            <textarea id="bio" name="bio" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old("bio") }}</textarea>
                            <x-input-error :messages="$errors->get("bio")" class="mt-2" />
                        </div>

                        <!-- Contact Phone -->
                        <div class="mt-4">
                            <x-input-label for="contact_phone" :value="__("Contact Phone (Optional)")" />
                            <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old("contact_phone")" autocomplete="tel" />
                            <x-input-error :messages="$errors->get("contact_phone")" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="mt-4">
                            <x-input-label for="location" :value="__("Location (e.g., City, Area)")" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old("location")" autocomplete="address-level2" />
                            <x-input-error :messages="$errors->get("location")" class="mt-2" />
                        </div>

                        <!-- Profile Picture -->
                        <div class="mt-4">
                            <x-input-label for="profile_picture" :value="__("Profile Picture (Optional)")" />
                            <input id="profile_picture" name="profile_picture" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                            <x-input-error :messages="$errors->get("profile_picture")" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __("Create Profile") }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

