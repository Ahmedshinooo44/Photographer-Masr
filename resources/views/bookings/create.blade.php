<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Book Photographer: ") }} {{ $photographerProfile->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">{{ __("Booking Request Details") }}</h3>

                    {{-- Display Validation Errors --}}
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

                    <form method="POST" action="{{ route("bookings.store", $photographerProfile) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Client Name --}}
                        <div>
                            <x-input-label for="client_name" :value="__("Your Name")" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old("client_name")" required autofocus />
                            <x-input-error :messages="$errors->get("client_name")" class="mt-2" />
                        </div>

                        {{-- Client Contact Number --}}
                        <div>
                            <x-input-label for="client_contact_number" :value="__("Contact Number")" />
                            <x-text-input id="client_contact_number" class="block mt-1 w-full" type="tel" name="client_contact_number" :value="old("client_contact_number")" required />
                            <x-input-error :messages="$errors->get("client_contact_number")" class="mt-2" />
                        </div>

                        {{-- Client Email (Optional) --}}
                        <div>
                            <x-input-label for="client_email" :value="__("Email Address (Optional)")" />
                            <x-text-input id="client_email" class="block mt-1 w-full" type="email" name="client_email" :value="old("client_email")" />
                            <x-input-error :messages="$errors->get("client_email")" class="mt-2" />
                        </div>

                        {{-- Event Date --}}
                        <div>
                            <x-input-label for="event_date" :value="__("Event Date")" />
                            <x-text-input id="event_date" class="block mt-1 w-full" type="datetime-local" name="event_date" :value="old("event_date")" required />
                            <x-input-error :messages="$errors->get("event_date")" class="mt-2" />
                        </div>

                        {{-- Event Location --}}
                        <div>
                            <x-input-label for="event_location" :value="__("Event Location")" />
                            <x-text-input id="event_location" class="block mt-1 w-full" type="text" name="event_location" :value="old("event_location")" required />
                            <x-input-error :messages="$errors->get("event_location")" class="mt-2" />
                        </div>

                        {{-- Event Details (Optional) --}}
                        <div>
                            <x-input-label for="event_details" :value="__("Event Details (Optional)")" />
                            <textarea id="event_details" name="event_details" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old("event_details") }}</textarea>
                            <x-input-error :messages="$errors->get("event_details")" class="mt-2" />
                        </div>

                        {{-- Payment Proof (Optional) --}}
                        <div>
                            <x-input-label for="payment_proof" :value="__("Payment Proof (Optional - JPG, PNG, PDF, max 5MB)")" />
                            <input id="payment_proof" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="payment_proof">
                            <x-input-error :messages="$errors->get("payment_proof")" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ms-4">
                                {{ __("Submit Booking Request") }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

