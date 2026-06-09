<?php
session_start();
// database login
include('db_credentials.php');

$game_id      = $_GET['game_id']      ?? '';
$current_turn = $_GET['current_turn'] ?? '';

// switch turn: 0 -> 1, 1 -> 0
$next_turn = ((int)$current_turn === 0) ? 1 : 0;

// sql statement to update the player turn
$sql = '
    UPDATE games 
    SET current_turn = :next_turn 
    WHERE game_id = :game_id
    ';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':next_turn', $next_turn, PDO::PARAM_INT);
$stmt->bindValue(':game_id',   $game_id,   PDO::PARAM_STR);
$stmt->execute();

// return a JSON response with the next turn
header('Content-Type: application/json');
echo json_encode(['current_turn' => $next_turn]);
