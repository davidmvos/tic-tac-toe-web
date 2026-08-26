<?php
require_once 'cors.php';
require_once 'env.php';
require_once 'get_payload.php';
require_once 'getGames.php';


function checkWinner($board) { // returns 'X', 'O', or null
    $winningCombinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // columns
        [0, 4, 8], [2, 4, 6]             // diagonals
    ];

    foreach ($winningCombinations as $combination) {
        list($a, $b, $c) = $combination;
        if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
            return $board[$a]; 
        }
    }

    return null; // no winner
}


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
    $playerId = getPayload()["playerId"];
    $field = getPayload()["field"];
} catch (\Throwable $th) {
    echo json_encode([
        "success" => false,
        "message" => "Faulty payload"
    ]);
}

$games = getGames($conn);



if (!isset($games[$gameId]) or $gameId<1000000 or $gameId>9999999) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect game id"
    ]);
} else {
if ( ($games[$gameId]["playerO"] != $playerId and $games[$gameId]["playerX"] != $playerId ) or $playerId<1000000 or $playerId>9999999) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect player id"
    ]);
} else {
if ($field < 0 or $field > 9) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect field"
    ]);
} else {

    if ($games[$gameId]["playerX"] == $playerId) {
        $playerSymbol = "X";
    } else {
        $playerSymbol = "O";
    }

    $gameState = json_decode($games[$gameId]["gameState"]);
    $stmt = $conn->prepare("UPDATE `tictactoe` SET `gameState`=? WHERE `gameId` LIKE ?");
    $stmt->bind_param("si", $gameState, $gameId);


    if ((($playerSymbol == "X" && count(array_keys($gameState, "X")) == count(array_keys($gameState, "O")) - 1) ||
         ($playerSymbol == "O" && count(array_keys($gameState, "X")) == count(array_keys($gameState, "O")))) &&
          $gameState[$field] == ""
        ){
        $gameState[$field] = $playerSymbol;
        $gameState = json_encode($gameState);
        $stmt->execute();
        $stmt->close();

        $gameState = json_decode($gameState);

        echo json_encode([
            "success" => true,
            "message" => "Made move",
            "field" => $field,
            "gameState" => $gameState
        ]);

        if (null !== checkWinner($gameState)) {
            ob_flush();
            flush();

            sleep(20);

            $stmt = $conn->prepare("DELETE FROM `tictactoe` WHERE `gameId` LIKE ?");
            $stmt->bind_param("i", $gameId);
            $stmt->execute();
            $stmt->close();

        }


    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid move!"
        ]);
    }


}
}
}

$conn->close();

} catch (\Throwable $th) {
echo json_encode([
    "success" => false,
    "message" => "Internal Error"
]);
}



?>