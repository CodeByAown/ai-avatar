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
                'model' => 'gpt-4-turbo', // Or gpt-4o
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful AI assistant. Keep your responses concise and suitable for a video avatar to speak.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 150,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error: ' . $response->body());
                throw new \Exception('Failed to communicate with OpenAI.');
            }

            return $response->json('choices.0.message.content') ?? 'I am sorry, I could not generate a response.';
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return "I'm having trouble connecting to my brain right now.";
        }
    }
}
