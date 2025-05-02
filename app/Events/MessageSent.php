<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * We need a private channel for each conversation.
     * The channel name could be derived from the conversation_id.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>|\Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel|array
    {
        // Use a private channel named after the conversation ID
        // Ensure conversation_id is consistent (e.g., always sorted user IDs or photographer_id_guest_id)
        return new PrivateChannel("conversation." . $this->message->conversation_id);
    }

    /**
     * The event"s broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return "new-message"; // Client will listen for ".new-message"
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // Return the message data, including sender info if needed
        return [
            "message" => $this->message->load(["sender:id,name"]), // Eager load sender for client display
        ];
    }
}

