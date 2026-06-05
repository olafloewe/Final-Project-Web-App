<?php
session_start();
// database login credentials
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// sql statement
$sql = "
    UPDATE games 
    SET winner = NULL, game_status = 3 
    WHERE game_id = :game_id;
";

// prepare and execute the SQL statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return a JSON response indicating the game is a tie
header('Content-Type: application/json');
echo json_encode(['tie' => true]);
?>