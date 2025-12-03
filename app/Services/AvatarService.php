<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AvatarService
{
    protected $apiKey;
    // Example using D-ID, but designed to be swappable
    protected $baseUrl = 'https://api.d-id.com'; 

    public function __construct()
    {
        $this->apiKey = config('services.avatar.api_key');
    }

    public function generateVideo(string $text): string
    {
        // This is a placeholder implementation for D-ID's "talks" endpoint
        // You would need to adjust this based on the specific provider (HeyGen, D-ID, etc.)
        
        try {
            $response = Http::withBasicAuth($this->apiKey, '') // D-ID uses Basic Auth with API Key as username
                ->post("{$this->baseUrl}/talks", [
                    'script' => [
                        'type' => 'text',
                        'input' => $text,
                        'provider' => [
                            'type' => 'microsoft',
                            'voice_id' => 'en-US-JennyNeural'
                        ]
                    ],
                    'source_url' => 'https://d-id-public-bucket.s3.us-west-2.amazonaws.com/alice.jpg', // Default avatar
                ]);

            if ($response->failed()) {
                Log::error('Avatar API Error: ' . $response->body());
                throw new \Exception('Failed to generate avatar video.');
            }

            // D-ID returns an ID, then you have to poll for the result.
            // For a real-time demo, we might want to use their Streaming API, 
            // but that requires a more complex WebSocket setup on the frontend.
            // For this MVP, we'll assume we get a URL or we handle the polling here (simplified).
            
            $id = $response->json('id');
            return $this->pollForVideo($id);

        } catch (\Exception $e) {
            Log::error('Avatar Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function pollForVideo($id)
    {
        // Simple polling mechanism (blocking for demo purposes, use Queues in production)
        $attempts = 0;
        while ($attempts < 10) {
            sleep(2);
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get("{$this->baseUrl}/talks/{$id}");
            
            $status = $response->json('status');
            if ($status === 'done') {
                return $response->json('result_url');
            }
            if ($status === 'error') {
                throw new \Exception('Avatar generation failed.');
            }
            $attempts++;
        }
        throw new \Exception('Avatar generation timed out.');
    }
}
