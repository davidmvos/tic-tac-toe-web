<?php
require_once 'cors.php';
function getPayload(): array {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    return is_array($data) ? $data : [];
}
?>


