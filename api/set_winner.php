<?php
session_start();
// database login
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$winner  = $_GET['winner']  ?? '';

$sql = '
    UPDATE games 
    SET winner = :winner 
    WHERE game_id = :game_id
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':winner',  $winner,  PDO::PARAM_INT);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return a JSON response with the winner
header('Content-Type: application/json');
echo json_encode(['winner' => (int)$winner]);