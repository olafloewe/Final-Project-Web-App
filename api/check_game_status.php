<?php
session_start();
// database login
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// sql statement to check the game status
$sql =  '
    SELECT game_status 
    FROM games 
    WHERE game_id = :game_id
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// fetch the result and return the game status as JSON
$data = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['game_status' => (int)($data['game_status'] ?? 0)]);
