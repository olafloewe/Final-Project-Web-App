<?php
session_start();
include('db_credentials.php');

$game_id      = $_GET['game_id']      ?? '';
$current_turn = $_GET['current_turn'] ?? '';

// switch turn: 0 -> 1, 1 -> 0
$next_turn = ((int)$current_turn === 0) ? 1 : 0;

$sql = 'UPDATE games SET current_turn = :next_turn WHERE game_id = :game_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':next_turn', $next_turn, PDO::PARAM_INT);
$stmt->bindValue(':game_id',   $game_id,   PDO::PARAM_STR);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['current_turn' => $next_turn]);
