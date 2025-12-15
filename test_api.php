<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = env('BEYOND_PRESENCE_API_KEY');
$avatarId = env('AVATAR_ID');
$baseUrl = 'https://api.bey.dev/v1';

echo "Testing BeyondPresence API...\n";
echo "URL: $baseUrl/sessions\n";
echo "Avatar ID: $avatarId\n";

try {
// 2. Test GET /calls (List calls)
    echo "Testing GET /calls...\n";
    $response = Http::withOptions(['verify' => false])
        ->withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])
        ->get($baseUrl . '/calls');
    
    echo "List Status: " . $response->status() . "\n";
    echo "List Body: " . $response->body() . "\n";

    $calls = json_decode($response->body(), true);

    // Handle pagination (data array)
    $results = $calls['data'] ?? [];

    if (!empty($results[0]['id'])) {
        $callId = $results[0]['id'];
        // 3. Test DELETE /calls/{id}
        echo "Testing DELETE /calls/$callId...\n";
        
        $delResponse = Http::withOptions(['verify' => false])
            ->withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->delete($baseUrl . "/calls/$callId");
            
        echo "Delete Status: " . $delResponse->status() . "\n";
        echo "Delete Body: " . $delResponse->body() . "\n";
    } else if (empty($results)) {
         echo "No active calls to delete.\n";
    } else {
        echo "Could not list calls to delete.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
