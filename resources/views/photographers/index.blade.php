@include('layouts.navigation')
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Photographers") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">{{ __("Browse Photographers") }}</h3>

                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-medium mb-2">{{ __('Filter') }}</h4>
                        <form action="{{ route('photographers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name...') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <select name="specialty" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                <option value="">{{ __('All Specialties') }}</option>
                                @foreach($specialties ?? [] as $specialty)
                                    <option value="{{ $specialty }}" {{ request('specialty') == $specialty ? 'selected' : '' }}>{{ $specialty }}</option>
                                @endforeach
                            </select>
                            <select name="location" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                <option value="">{{ __('All Locations') }}</option>
                                @foreach($locations ?? [] as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                                @endforeach
                            </select>
                            <div class="md:col-span-3 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                    {{ __('Apply Filters') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($photographerProfiles->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                            @foreach ($photographerProfiles as $profile)
                                <div class="group bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 border border-gray-200 dark:border-gray-600">
                                    <a href="{{ route("photographers.show", $profile) }}" class="block">
                                        <div class="relative h-56 w-full overflow-hidden">
                                            @if ($profile->profile_picture_path)
                                                <img src="{{ Storage::url($profile->profile_picture_path) }}" alt="{{ $profile->user->name }}" class="w-full h-full object-cover transition duration-300 ease-in-out group-hover:scale-105">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-5">
                                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-1 truncate group-hover:text-orange-600 dark:group-hover:text-orange-400">{{ $profile->user->name }}</h4>
                                            @if($profile->specialties)
                                                <p class="text-sm text-orange-600 dark:text-orange-400 mb-2">{{ $profile->specialties }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 flex items-center">
                                                <svg class="w-4 h-4 ms-1.5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                                {{ $profile->location ?? __("Location not specified") }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $profile->bio ?? __("No bio available.") }}</p>
                                            <span class="inline-block mt-3 text-sm font-medium text-orange-600 dark:text-orange-400 group-hover:underline">{{ __('View Profile') }} &rarr;</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        @if ($photographerProfiles->hasPages())
                            <div class="mt-8">
                                {{ $photographerProfiles->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">{{ __("No Photographers Found") }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __("There are currently no photographer profiles available.") }}</p>
                            @auth
                                @if(auth()->user()->role === 'photographer' && !auth()->user()->photographerProfile)
                                    <div class="mt-6">
                                        <a href="{{ route('my-profile.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            {{ __('Create Your Profile') }}
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
