<?php
require_once 'cors.php';
function getGames($mysqli) {
    $sql = "SELECT * FROM `tictactoe`";
    $result = $mysqli->query($sql);

    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $gameId = $row['gameId'];
            $data[$gameId] = $row;
        }
    
    }

    return $data;
}





?>