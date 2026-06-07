<?php
session_start();
include('db_credentials.php');

$game_id    = $_GET['game_id']    ?? '';
$game_state = $_GET['game_state'] ?? '';

$sql = 'UPDATE games SET game_state = :game_state WHERE game_id = :game_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_state', $game_state, PDO::PARAM_STR);
$stmt->bindValue(':game_id',    $game_id,    PDO::PARAM_STR);
$stmt->execute();

header('Content-Type: application/json');
echo json_encode(['success' => true]);
