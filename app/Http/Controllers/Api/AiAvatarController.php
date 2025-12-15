<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BeyondPresenceService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiAvatarController extends Controller
{
    protected $beyondPresence;
    protected $openAI;

    public function __construct(BeyondPresenceService $beyondPresence, OpenAIService $openAI)
    {
        $this->beyondPresence = $beyondPresence;
        $this->openAI = $openAI;
    }

    /**
     * Initialize the chat session.
     */
    public function startSession()
    {
        try {
            $sessionData = $this->beyondPresence->startSession();
            return response()->json($sessionData);
        } catch (\Exception $e) {
            Log::error('AI Avatar Controller Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process a user message and return an avatar video response.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $userMessage = $request->input('text');

        try {
            // 1. Get text response from OpenAI
            $aiResponseText = $this->openAI->generateResponse($userMessage);

            if (!$aiResponseText) {
                return response()->json(['error' => 'Failed to get AI response.'], 500);
            }

            // 2. Generate video from BeyondPresence
            $videoUrl = $this->beyondPresence->generateVideo($aiResponseText);

            if (!$videoUrl) {
                return response()->json(['error' => 'Failed to generate avatar video.'], 500);
            }

            return response()->json([
                'text_response' => $aiResponseText,
                'video_url' => $videoUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('AI Avatar Controller Error: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function resetSession()
    {
        try {
            $count = $this->beyondPresence->resetSessions();
            return response()->json(['message' => "Cleared $count active sessions."]);
        } catch (\Exception $e) {
            Log::error('AI Avatar Controller Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
