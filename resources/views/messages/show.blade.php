<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("Conversation with") }} {{ $otherParticipant->name ?? __("Guest") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg flex flex-col" style="height: 70vh;">
                {{-- Message Display Area --}}
                <div id="message-list" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
                    @forelse ($messages as $message)
                        <div class="flex {{ $message->sender_user_id == Auth::id() ? "justify-end" : "justify-start" }}">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow {{ $message->sender_user_id == Auth::id() ? "bg-orange-500 text-white" : "bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200" }}">
                                <p class="text-sm">{{ $message->content }}</p>
                                <p class="text-xs mt-1 text-right {{ $message->sender_user_id == Auth::id() ? "text-orange-100" : "text-gray-500 dark:text-gray-400" }}">
                                    {{ $message->created_at->format("M d, H:i") }}
                                    @if ($message->sender_user_id == Auth::id() && $message->read_at)
                                        <span title="{{ __("Read at") }} {{ $message->read_at->format("M d, H:i") }}">&#10003;</span> {{-- Checkmark for read --}}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">{{ __("No messages in this conversation yet.") }}</p>
                    @endforelse
                </div>

                {{-- Message Input Area --}}
                <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900">
                    {{-- Basic form for now, will be enhanced with JS for real-time --}}
                    <form id="message-form" method="POST" action="{{ route("messages.store") }}" class="flex items-center gap-3">
                        @csrf
                        {{-- Need recipient_user_id and conversation_id (or derive it) --}}
                        <input type="hidden" name="recipient_user_id" value="{{ $otherParticipant->id ?? "" }}"> {{-- Needs correct ID --}}
                        <input type="hidden" name="conversation_id" value="{{ $conversationId }}">
                        
                        <textarea 
                            name="content" 
                            rows="1" 
                            class="flex-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm resize-none" 
                            placeholder="{{ __("Type your message...") }}"
                            required
                        ></textarea>
                        
                        <x-primary-button type="submit">
                            {{ __("Send") }}
                        </x-primary-button>
                    </form>
                    <div id="message-error" class="text-red-500 text-sm mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    @push("scripts")
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const messageList = document.getElementById("message-list");
            const messageForm = document.getElementById("message-form");
            const messageInput = messageForm.querySelector("textarea[name=\"content\"]");
            const messageError = document.getElementById("message-error");
            const conversationId = "{{ $conversationId }}";
            const currentUserId = {{ Auth::id() ?? "null" }}; // Get current user ID

            // Function to append message to the list
            const appendMessage = (messageData) => {
                const message = messageData.message; // The actual message object is nested
                const isSender = message.sender_user_id === currentUserId;
                const messageDiv = document.createElement("div");
                messageDiv.classList.add("flex", isSender ? "justify-end" : "justify-start");
                
                const messageBubble = document.createElement("div");
                messageBubble.classList.add("max-w-xs", "lg:max-w-md", "px-4", "py-2", "rounded-lg", "shadow");
                messageBubble.classList.add(isSender ? "bg-orange-500" : "bg-gray-200", isSender ? "text-white" : "text-gray-800");
                if (!isSender) {
                    messageBubble.classList.add("dark:bg-gray-700", "dark:text-gray-200");
                }

                const contentP = document.createElement("p");
                contentP.classList.add("text-sm");
                contentP.textContent = message.content;

                const timeP = document.createElement("p");
                timeP.classList.add("text-xs", "mt-1", "text-right");
                timeP.classList.add(isSender ? "text-orange-100" : "text-gray-500", "dark:text-gray-400");
                // Format time simply for now
                const messageDate = new Date(message.created_at);
                timeP.textContent = messageDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

                messageBubble.appendChild(contentP);
                messageBubble.appendChild(timeP);
                messageDiv.appendChild(messageBubble);
                messageList.appendChild(messageDiv);

                // Scroll to bottom
                messageList.scrollTop = messageList.scrollHeight;
            };

            // Scroll to bottom on initial load
            messageList.scrollTop = messageList.scrollHeight;

            // AJAX form submission (prevents page reload, relies on Echo for display)
            messageForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                messageError.textContent = "";
                const formData = new FormData(messageForm);
                const content = formData.get("content").trim();

                if (!content) return;

                // Disable input while sending
                messageInput.disabled = true;
                messageForm.querySelector("button[type=\"submit\"]").disabled = true;

                try {
                    const response = await fetch(messageForm.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": formData.get("_token"),
                            "Accept": "application/json",
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || "Failed to send message");
                    }

                    // Clear input ONLY after successful send
                    messageInput.value = "";

                } catch (error) {
                    console.error("Send message error:", error);
                    messageError.textContent = error.message || "Could not send message.";
                } finally {
                    // Re-enable input
                    messageInput.disabled = false;
                    messageForm.querySelector("button[type=\"submit\"]").disabled = false;
                    messageInput.focus(); // Focus back on input
                }
            });

            // Listen for messages on the private channel
            if (window.Echo && conversationId && currentUserId) {
                window.Echo.private(`conversation.${conversationId}`)
                    .listen(".new-message", (e) => {
                        console.log("Received message:", e);
                        appendMessage(e); // e contains { message: { ... } }
                    });
                console.log(`Listening on channel: conversation.${conversationId}`);
            } else {
                console.error("Echo not configured, or conversationId/currentUserId missing.");
            }
        });
    </script>
    @endpush

</x-app-layout>

