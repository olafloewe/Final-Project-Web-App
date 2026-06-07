<?php
session_start();
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// winner = NULL, game_status = 3 signals a completed tie
$sql = 'UPDATE games SET winner = NULL, game_status = 3 WHERE game_id = :game_id AND (winner IS NULL OR winner = -1)';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['tie' => true]);
