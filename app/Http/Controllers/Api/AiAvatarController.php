<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenAIService;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiAvatarController extends Controller
{
    protected $openAI;
    protected $avatar;

    public function __construct(OpenAIService $openAI, AvatarService $avatar)
    {
        $this->openAI = $openAI;
        $this->avatar = $avatar;
    }

    public function start()
    {
        return response()->json([
            'message' => 'Session started',
            'status' => 'ready'
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'text' => 'required|string', // Assuming STT happens on frontend for now to save complexity
        ]);

        try {
            $userMessage = $request->input('text');
            
            // 1. Get text response from OpenAI
            $aiResponseText = $this->openAI->generateResponse($userMessage);

            // 2. Convert text to video
            $videoUrl = $this->avatar->generateVideo($aiResponseText);

            return response()->json([
                'text' => $aiResponseText,
                'video_url' => $videoUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('AI Processing Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong processing your request.'], 500);
        }
    }
}
