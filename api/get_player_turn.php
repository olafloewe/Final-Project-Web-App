<?php
session_start();
// database login
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// sql statement to check the current player's turn
$sql = '
    SELECT current_turn 
    FROM games 
    WHERE game_id = :game_id
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// fetch the result and return the current player's turn as JSON
$data         = $stmt->fetch(PDO::FETCH_ASSOC);
$current_turn = $data['current_turn'] ?? null;

header('Content-Type: application/json');
echo json_encode(['current_turn' => (int)$current_turn]);
