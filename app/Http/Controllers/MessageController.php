<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\PhotographerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
// Import Event for broadcasting
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Start or retrieve a conversation with a photographer.
     *
     * @param  \App\Models\PhotographerProfile $photographerProfile
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConversation(PhotographerProfile $photographerProfile)
    {
        $user = Auth::user();
        if (!$user) {
            // Redirect guests to login or handle guest messaging differently
            return redirect()->route("login")->with("error", __("Please log in to contact the photographer."));
        }

        $photographerUserId = $photographerProfile->user_id;

        // Prevent photographer from messaging themselves
        if ($user->id === $photographerUserId) {
            return redirect()->back()->with("error", __("You cannot start a conversation with yourself."));
        }

        // Determine conversation ID (consistent sorting)
        $participants = [$user->id, $photographerUserId];
        sort($participants);
        $conversationId = "user_" . $participants[0] . "_user_" . $participants[1];

        // Redirect to the conversation view
        return redirect()->route("messages.show", $conversationId);
    }

    /**
     * Display messages for a specific conversation.
     *
     * @param  string $conversationId
     * @return \Illuminate\Http\JsonResponse | \Illuminate\View\View
     */
    public function index(Request $request, string $conversationId)
    {
        // Validate conversationId format if needed

        // Determine participants based on conversationId or request context
        // This logic needs refinement based on how conversationId is structured
        // e.g., "photographer_5_guest_xyz" or "user_1_user_5"

        // For now, assume the logged-in user is part of the conversation
        $user = Auth::user();
        if (!$user) {
            // Handle guest access - requires guest_identifier logic
            // For now, restrict to logged-in users
            // If allowing guests, need to fetch guest identifier from session/cookie
            return redirect()->route("login")->with("error", __("Authentication required to view messages."));
        }

        // Extract participant IDs from conversationId
        $participantIds = [];
        if (preg_match("/^user_(\d+)_user_(\d+)$/", $conversationId, $matches)) {
            $participantIds = [(int)$matches[1], (int)$matches[2]];
        } elseif (preg_match("/^photographer_(\d+)_guest_(.+)$/", $conversationId, $matches)) {
            // Handle guest conversation logic if implemented
            // $participantIds = [(int)$matches[1]]; // Photographer ID
            // $guestIdentifier = $matches[2];
            // For now, focus on user-to-user
            abort(404, __("Guest conversations not supported yet."));
        } else {
            abort(404, __("Invalid conversation ID format."));
        }

        // Ensure the logged-in user is part of this conversation
        if (!in_array($user->id, $participantIds)) {
            abort(403, __("Unauthorized access to conversation."));
        }

        // Determine the other participant's ID
        $otherParticipantId = ($participantIds[0] === $user->id) ? $participantIds[1] : $participantIds[0];
        $otherParticipant = User::find($otherParticipantId);

        if (!$otherParticipant) {
             abort(404, __("Conversation partner not found."));
        }

        $messages = Message::where("conversation_id", $conversationId)
                           ->with(["sender:id,name", "recipient:id,name"]) // Eager load sender/recipient names
                           ->orderBy("created_at", "asc")
                           ->get();

        // Mark messages as read if the current user is the recipient
        Message::where("conversation_id", $conversationId)
               ->where("recipient_user_id", $user->id)
               ->whereNull("read_at")
               ->update(["read_at" => now()]);

        // Return as JSON for API/JS frontend or pass to a view
        if ($request->expectsJson()) {
            return response()->json(["messages" => $messages, "otherParticipant" => $otherParticipant]);
        } else {
            // Pass data to a Blade view (to be created)
            return view("messages.show", compact("messages", "conversationId", "otherParticipant"));
        }
    }

    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "recipient_user_id" => ["required", "exists:users,id"],
            "content" => ["required", "string"],
            // "guest_identifier" => ["nullable", "string", "max:255"], // If sent by a guest
            "conversation_id" => ["required", "string", "max:255"], // Should be provided by the form
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $sender = Auth::user(); // Assume sender is logged in
        $recipientId = $validatedData["recipient_user_id"];
        $conversationId = $validatedData["conversation_id"];

        if (!$sender) {
             return response()->json(["error" => "Authentication required"], 401);
        }
        
        // Verify conversation ID matches participants
        $expectedParticipants = [$sender->id, (int)$recipientId];
        sort($expectedParticipants);
        $expectedConversationId = "user_" . $expectedParticipants[0] . "_user_" . $expectedParticipants[1];

        if ($conversationId !== $expectedConversationId) {
             return response()->json(["error" => "Conversation ID mismatch"], 400);
        }

        // Prevent sending message to self
        if ($sender->id === (int)$recipientId) {
            return response()->json(["error" => "Cannot send message to yourself"], 400);
        }

        $validatedData["sender_user_id"] = $sender->id;
        // $validatedData["guest_identifier"] = null; // Assuming no guests for now

        try {
            $message = Message::create($validatedData);
            $message->load(["sender:id,name", "recipient:id,name"]); // Load relationships for broadcast

            // Broadcast the message event
            broadcast(new MessageSent($message))->toOthers(); // Use toOthers() to not send back to the sender's browser tab

            return response()->json($message, 201);

        } catch (\Exception $e) {
            // Log the error
            
            return response()->json(["error" => "Failed to send message. Please try again."], 500);
        }
    }

    // Add methods for listing conversations later (for dashboard)
}

