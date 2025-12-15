<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function generateResponse(string $prompt): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => 'gpt-4o', // Changed from gpt-4-turbo to gpt-4o for better availability
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful AI assistant. Keep your responses concise and suitable for a video avatar to speak.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 150,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error: ' . $response->body());
                // Fallback for demo/testing when quota is exceeded
                return "Hello! I am your AI assistant. I am currently in demo mode because my brain is out of credits, but I can still talk to you!";
            }

            return $response->json('choices.0.message.content') ?? 'I am sorry, I could not generate a response.';
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return "Hello! I am your AI assistant. I am ready to help.";
        }
    }

    public function generateSpeech(string $text): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/audio/speech", [
                'model' => 'tts-1',
                'input' => $text,
                'voice' => env('VOICE_ID', 'alloy'), // Default to alloy if not set
            ]);

            if ($response->successful()) {
                // Return the binary audio data
                return $response->body();
            }

            Log::error('OpenAI TTS Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI TTS Exception: ' . $e->getMessage());
            return null;
        }
    }
}
