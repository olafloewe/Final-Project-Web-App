<?php
session_start();
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

$sql = 'SELECT game_status FROM games WHERE game_id = :game_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['game_status' => (int)($data['game_status'] ?? 0)]);
