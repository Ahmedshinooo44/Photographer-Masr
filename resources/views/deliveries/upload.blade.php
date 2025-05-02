<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Upload Final Files for Booking #:booking_id", ["booking_id" => $booking->id]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __("Booking Details") }}
                    </h3>
                    <div class="mb-6 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>{{ __("Client") }}:</strong> {{ $booking->client_name }}</p>
                        <p><strong>{{ __("Event Date") }}:</strong> {{ $booking->event_date->format("Y-m-d H:i") }}</p>
                        <p><strong>{{ __("Event Location") }}:</strong> {{ $booking->event_location }}</p>
                    </div>

                    <form method="POST" action="{{ route("deliveries.upload.handle", $booking) }}" enctype="multipart/form-data">
                        @csrf
                        @method("POST") {{-- Explicitly POST --}}

                        {{-- File Input --}}
                        <div class="mb-4">
                            <x-input-label for="final_files" :value="__("Select Final Files (Images or Zip)")" />
                            <input id="final_files" name="final_files[]" type="file" multiple class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">{{ __("You can select multiple image files (JPG, PNG) or a single ZIP file. Max size per file: 100MB.") }}</p>
                            <x-input-error :messages="$errors->get("final_files")" class="mt-2" />
                            <x-input-error :messages="$errors->get("final_files.*")" class="mt-2" />
                        </div>

                        {{-- Optional: Update Delivery Deadline --}}
                        <div class="mb-6">
                            <x-input-label for="delivery_deadline" :value="__("Update Delivery Deadline (Optional)")" />
                            <x-text-input id="delivery_deadline" class="block mt-1 w-full" type="date" name="delivery_deadline" :value="old("delivery_deadline", $booking->delivery_deadline ? $booking->delivery_deadline->format("Y-m-d") : "")" />
                            <x-input-error :messages="$errors->get("delivery_deadline")" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __("Upload Files & Generate Link") }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

