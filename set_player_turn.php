<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$current_turn = (int)$_GET['current_turn'] ?? '';

// game has not started yet, set current_turn to 0
if ($current_turn === -1) $current_turn = 0;
// game has started, toggle current_turn between 0 and 1
else $current_turn = $current_turn === 0 ? 1 : 0;

// sql query to update current turn
$sql = "
    UPDATE games
    SET current_turn = :current_turn
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':current_turn', $current_turn, PDO::PARAM_STR);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return current turn as json
header('Content-Type: application/json');
echo json_encode(['current_turn' => $current_turn]);
?>