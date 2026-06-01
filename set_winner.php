<?php
session_start();
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$winner = $_GET['winner'] ?? '';

$sql = "
    UPDATE games
    SET winner = :winner
    WHERE game_id = :game_id;
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':winner', $winner, PDO::PARAM_INT);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['winner' => (int)$winner]);
?>