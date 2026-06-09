<?php
session_start();
// database login
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// winner = NULL, game_status = 3 signals a completed tie
$sql = '
    UPDATE games 
    SET winner = NULL, game_status = 3 
    WHERE game_id = :game_id AND (winner IS NULL OR winner = -1)
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return a JSON response indicating the game is a tie
header('Content-Type: application/json');
echo json_encode(['tie' => true]);
