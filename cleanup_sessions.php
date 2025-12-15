<?php

require __DIR__ . '/vendor/autoload.php';

use Agence104\LiveKit\RoomServiceClient;
use Illuminate\Support\Facades\Http;

// Load environment variables if not using full Laravel boot
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = str_replace('wss://', 'https://', $_ENV['LIVEKIT_URL'] ?? '');
$apiKey = $_ENV['LIVEKIT_API_KEY'] ?? null;
$apiSecret = $_ENV['LIVEKIT_API_SECRET'] ?? null;

if (!$host || !$apiKey || !$apiSecret) {
    echo "Error: LiveKit credentials missing from .env\n";
    exit(1);
}

echo "Connecting to LiveKit at $host...\n";

$svc = new RoomServiceClient($host, $apiKey, $apiSecret, [
    'httpClient' => new GuzzleHttp\Client(['verify' => false])
]);

try {
    // List rooms
    $rooms = $svc->listRooms();
    
    echo "Found " . count($rooms) . " active rooms.\n";

    foreach ($rooms as $room) {
        $name = $room->getName();
        echo "Deleting room: $name... ";
        $svc->deleteRoom($name);
        echo "Done.\n";
    }

    echo "All sessions cleared.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
