<?php
require_once 'cors.php';
require_once 'env.php';
require_once 'get_payload.php';
require_once 'getGames.php';


try {
$conn = new mysqli($server, $username, $password, $database);
if ($conn->connect_error) 
{
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
} 

try {
    # gameid, playerid, field
    $gameId = getPayload()["gameId"];
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "Faulty payload"
    ]);
}
$games = getGames($conn);

if (!isset($gameId) or $gameId < 1000000 or $gameId > 9999999 or !isset($games[$gameId])) {
    echo json_encode([
        "success" => false,
        "message" => "Wrong game ID"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "message" => "Output game state",
        "gameState" => json_decode($games[$gameId]["gameState"])
    ]);
}

} catch (\Throwable $th) {
echo json_encode([
    "success" => false,
    "message" => "Internal error"
]);
}
?>