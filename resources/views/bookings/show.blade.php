<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Booking Details") }} - {{ $booking->client_name }} ({{ $booking->event_date->format("Y-m-d") }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Booking Information --}}
            <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">{{ __("Booking Information") }}</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Client Name:") }}</span>
                            <span class="ml-2">{{ $booking->client_name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Contact Number:") }}</span>
                            <span class="ml-2">{{ $booking->client_contact_number }}</span>
                        </div>
                        @if($booking->client_email)
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Client Email:") }}</span>
                            <span class="ml-2">{{ $booking->client_email }}</span>
                        </div>
                        @endif
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Event Date & Time:") }}</span>
                            <span class="ml-2">{{ $booking->event_date->format("l, F j, Y \a\t H:i") }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Event Location:") }}</span>
                            <span class="ml-2">{{ $booking->event_location }}</span>
                        </div>
                        @if($booking->event_details)
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Event Details:") }}</span>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $booking->event_details }}</p>
                        </div>
                        @endif
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Booking Status:") }}</span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($booking->status)
                                    @case("pending") bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                    @case("confirmed") bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                    @case("completed") bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                    @case("rejected") bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                    @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                @endswitch">
                                {{ __(ucfirst($booking->status)) }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Delivery Deadline:") }}</span>
                            <span class="ml-2">{{ $booking->delivery_deadline ? $booking->delivery_deadline->format("Y-m-d") : __("Not set") }}</span>
                        </div>
                        @if($booking->payment_proof_path)
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __("Payment Proof:") }}</span>
                            <a href="{{ Storage::url($booking->payment_proof_path) }}" target="_blank" class="ml-2 text-indigo-600 dark:text-indigo-400 hover:underline">{{ __("View Proof") }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions & File Upload --}}
            <div class="md:col-span-1 space-y-8">
                {{-- Display Session Status/Errors --}}
                <x-auth-session-status class="mb-4" :status="session("success")" />
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session("error"))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-md">
                        {{ session("error") }}
                    </div>
                @endif

                {{-- Upload Final Images Form --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                    <form method="POST" action="{{ route("bookings.upload", $booking) }}" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                        @csrf
                        @method("PATCH") {{-- Use PATCH for update --}}

                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __("Upload Final Images & Update Status") }}</h3>

                        {{-- Final Images Upload --}}
                        <div>
                            <x-input-label for="final_images" :value="__("Final Images (ZIP file, max 100MB)")" />
                            <input id="final_images" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="final_images" accept=".zip">
                            <x-input-error :messages="$errors->get("final_images")" class="mt-2" />
                            @if($booking->final_images_link)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __("Current file uploaded.") }} 
                                    @if($downloadUrl)
                                        <a href="{{ $downloadUrl }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __("Get Temporary Download Link") }}</a>
                                    @else
                                        ({{ __("Link generation failed or file missing.") }})
                                    @endif
                                </p>
                            @endif
                        </div>

                        {{-- Delivery Deadline --}}
                        <div>
                            <x-input-label for="delivery_deadline" :value="__("Delivery Deadline (Optional)")" />
                            <x-text-input id="delivery_deadline" class="block mt-1 w-full" type="date" name="delivery_deadline" :value="old("delivery_deadline", $booking->delivery_deadline ? $booking->delivery_deadline->format("Y-m-d") : "")" />
                            <x-input-error :messages="$errors->get("delivery_deadline")" class="mt-2" />
                        </div>

                        {{-- Mark as Completed --}}
                        @if($booking->status !== "completed")
                        <div class="block mt-4">
                            <label for="status_completed" class="inline-flex items-center">
                                <input id="status_completed" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="status" value="completed">
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __("Mark as Completed") }}</span>
                            </label>
                        </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __("Update Booking") }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Other Actions (e.g., Update Status, Contact Client) --}}
                {{-- Add other action forms/buttons here as needed --}}

            </div>
        </div>
    </div>
</x-app-layout>

