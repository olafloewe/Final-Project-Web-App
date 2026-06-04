<?php
session_start();
// login to database
include('db_credentials.php');

// get game id
$game_id = $_GET['game_id'];

// SQL query to get game status
$sql = "
    SELECT game_status
    FROM games 
    WHERE game_id = :game_id;
    ";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// fetch game status and return as int JSON
$data = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode(['game_status' => (int)($data['game_status'])]);
?>