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
$games = getGames($conn);


    
try {
    $playerGameId = getPayload()["gameId"];
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "Faulty payload"
    ]);
}


if (!isset($games[$playerGameId]) or $playerGameId<1000000 or $playerGameId>9999999) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect game id"
    ]);
} else {
    $gameId = $playerGameId;
    $stmt = $conn->prepare("UPDATE `tictactoe` SET `playerXconnected`=1 WHERE `gameId` LIKE ?");
    $stmt->bind_param("i", $gameId);
    $gameId = $playerGameId;

    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Game joined",
        "gameId" => $playerGameId,
        "playerId" => $games[$playerGameId]["playerX"]
    ]);
}


$conn -> close();

}  catch (\Throwable $th) {
echo json_encode([
    "success" => false,
    "message" => "Internal Error"
]);
}


?>