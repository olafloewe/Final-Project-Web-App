<?php
session_start();
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$current_turn = $_GET['current_turn'] ?? '';

// if the game just started
if (empty($current_turn)) $current_turn = 0;
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

header("Location: board.php");
exit();
?>