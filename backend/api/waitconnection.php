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
} else {
$games = getGames($conn);


    
try {
    $gameId = getPayload()["gameId"];
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "Faulty payload"
    ]);
}


if (!isset($games[$gameId]) or $gameId<1000000 or $gameId>9999999) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect game id"
    ]);
} else {

    $otherPlayerConnected = intval($games[$gameId]["playerXconnected"]) > 0;
    echo json_encode([
        "success" => true,
        "message" => "TBD",
        "otherPlayerConnected" => $otherPlayerConnected,
    ]);
    
}



$conn -> close();

}
}  catch (\Throwable $th) {
echo json_encode([
    "success" => false,
    "message" => "Internal Error"
]);
}
?>