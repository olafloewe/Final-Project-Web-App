<?php
function set_current_turn($game_code, $current_turn) {
    // if the game just started
    if (empty($current_turn)) $current_turn = 0;
    // set current_turn to the other player
    else $current_turn = $current_turn === 0 ? 1 : 0;

    $sql = "
        UPDATE games
        SET current_turn = :current_turn
        WHERE game_code = :game_code AND game_status != 0;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':current_turn', $current_turn, PDO::PARAM_STR);
    $stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    return $current_turn;
}

include('db_credentials.php');

$sql = "
    SELECT current_turn 
    FROM games
    WHERE game_code = :game_code AND game_status != 0;
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);

$current_turn = $data['current_turn'] ?? '';

if (empty(current_turn)) {
    return set_current_turn($game_code, $current_turn);
}

return $current_turn;
?>