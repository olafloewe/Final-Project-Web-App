<?php
session_start();
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$current_turn = (int)$_GET['current_turn'] ?? '';

// if the game just started
if ($current_turn === -1) $current_turn = 0;
// set current_turn to the other player
else $current_turn = $current_turn === 0 ? 1 : 0;

$sql = "
    UPDATE games
    SET current_turn = :current_turn
    WHERE game_id = :game_id;
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':current_turn', $current_turn, PDO::PARAM_STR);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['current_turn' => $current_turn]);
?>