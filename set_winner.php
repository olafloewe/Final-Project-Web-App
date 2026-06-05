<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$winner = $_GET['winner'] ?? '';

// sql query to update winner
$sql = "
    UPDATE games
    SET winner = :winner
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':winner', $winner, PDO::PARAM_INT);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return winner as json
header('Content-Type: application/json');
echo json_encode(['winner' => (int)$winner]);
?>