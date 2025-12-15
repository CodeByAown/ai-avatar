<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\VideoGrant;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\RoomServiceClient;
use GuzzleHttp\Client;

class BeyondPresenceService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.bey.dev/v1'; // Corrected URL based on verification

    public function __construct()
    {
        $this->apiKey = env('BEYOND_PRESENCE_API_KEY');
    }

    /**
     * Start a real-time session with the avatar.
     * 
     * @return array|null Session details (url, token, room_name)
     */
    public function startSession()
    {
        try {
            $agentId = '37f38f4b-a31a-4b1c-b10b-a98f350f1a3c'; // The Managed Agent

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.bey.dev/v1/calls', [
                'agent_id' => $agentId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'url' => $data['livekit_url'] ?? null,
                    'token' => $data['livekit_token'] ?? null,
                    'room_name' => $data['id'] ?? null, // Call ID as room name
                ];
            }

            $errorMessage = $response->json('detail') ?? $response->body();
            Log::error('BeyondPresence Call Error: ' . $errorMessage);
            throw new \Exception('BeyondPresence API Error: ' . (is_string($errorMessage) ? $errorMessage : json_encode($errorMessage)));

        } catch (\Exception $e) {
            Log::error('BeyondPresence Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function closeSession($roomName)
    {
        // Not supported for Managed Agents via /v1/calls
        return false;
    }

    public function resetSessions()
    {
        // Not supported for Managed Agents via /v1/calls
        return 0;
    }



    /**
     * Generate a video response from text.
     * 
     * @param string $text
     * @return string|null Video URL or Stream URL
     */
    public function generateVideo(string $text)
    {
        try {
            $payload = [
                'avatar_id' => env('AVATAR_ID'),
                'text' => $text,
                'stream' => true,
            ];

            $voiceId = env('VOICE_ID');
            if ($voiceId && $voiceId !== 'your_voice_id') {
                $payload['voice_id'] = $voiceId;
            }

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/avatar/speak', $payload);

            if ($response->successful()) {
                return $response->json()['video_url'] ?? $response->json()['stream_url'] ?? null;
            }

            Log::error('BeyondPresence API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('BeyondPresence Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
