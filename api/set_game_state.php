<?php
session_start();
// database login
include('db_credentials.php');

$game_id    = $_GET['game_id']    ?? '';
$game_state = $_GET['game_state'] ?? '';

// sql statement to update the game state
$sql = '
    UPDATE games 
    SET game_state = :game_state 
    WHERE game_id = :game_id
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_state', $game_state, PDO::PARAM_STR);
$stmt->bindValue(':game_id',    $game_id,    PDO::PARAM_STR);
$stmt->execute();

// return a JSON response indicating success
header('Content-Type: application/json');
echo json_encode(['success' => true]);
