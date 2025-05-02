<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $photographerProfile->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Profile Header Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8">
                    {{-- Profile Picture --}}
                    <div class="flex-shrink-0 w-32 h-32 md:w-48 md:h-48">
                        @if ($photographerProfile->profile_picture_path)
                            <img src="{{ Storage::url($photographerProfile->profile_picture_path) }}" alt="{{ $photographerProfile->user->name }}" class="w-full h-full object-cover rounded-full shadow-md border-4 border-orange-200 dark:border-orange-700">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 shadow-md border-4 border-orange-200 dark:border-orange-700">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Profile Details & Actions --}}
                    <div class="flex-1 text-center md:text-start">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $photographerProfile->user->name }}</h3>
                        
                        {{-- Location --}}
                        @if($photographerProfile->location)
                        <p class="text-md text-gray-600 dark:text-gray-400 mb-4 flex items-center justify-center md:justify-start">
                            <svg class="w-5 h-5 ms-1.5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            {{ $photographerProfile->location }}
                        </p>
                        @endif

                        {{-- Bio --}}
                        <p class="text-gray-700 dark:text-gray-300 mb-6">{{ $photographerProfile->bio ?? __("No bio provided.") }}</p>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                            {{-- Booking Button --}}
                            <a href="{{ route("bookings.create", $photographerProfile) }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 dark:focus:ring-offset-gray-800">
                                {{ __("Book Now") }}
                            </a>
                            {{-- Contact Button (Functionality later) --}}
                            <a href="{{ route('messages.start', $photographerProfile) }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-gray-500 text-base font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __("Contact Photographer") }}
                            </a>
                        </div>
                        
                        {{-- Edit button if viewing own profile --}}
                        @auth
                            @if(Auth::id() == $photographerProfile->user_id)
                                <div class="mt-6">
                                    <a href="{{ route("my-profile.edit") }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                        {{ __("Edit My Profile") }}
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Portfolio Photos Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 md:p-8">
                <h4 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">{{ __("Portfolio") }}</h4>
                @if ($photographerProfile->photos->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach ($photographerProfile->photos as $photo)
                            <div class="group relative aspect-square overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                                <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->caption ?? "Portfolio image" }}" class="w-full h-full object-cover transition duration-300 ease-in-out group-hover:scale-105">
                                {{-- Optional: Overlay with caption on hover --}}
                                @if($photo->caption)
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-300 flex items-end p-2">
                                        <p class="text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">{{ $photo->caption }}</p>
                                    </div>
                                @endif
                                {{-- Add link/modal for larger view if needed --}}
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">{{ __("Portfolio Empty") }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __("This photographer hasn't uploaded any portfolio images yet.") }}</p>
                        {{-- Optional: Link for photographer to upload photos --}}
                        @auth
                            @if(Auth::id() == $photographerProfile->user_id)
                                <div class="mt-6">
                                    {{-- Link to photo upload page/modal (to be created) --}}
                                    <a href="#" {{-- Replace with actual route --}} class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 dark:focus:ring-offset-gray-800">
                                        {{ __("Upload Photos") }}
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            {{-- Booking Section Placeholder --}}
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 md:p-8">
                <h4 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">{{ __("Make a Booking Request") }}</h4>
                <p class="text-gray-600 dark:text-gray-400">Booking form will be implemented here.</p>
            </div> --}}

        </div>
    </div>
</x-app-layout>

