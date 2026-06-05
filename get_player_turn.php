<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'];

// sql query to get current turn
$sql = "
    SELECT current_turn
    FROM games 
    WHERE game_id = :game_id;
    ";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return current turn as int json
$data = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode(['current_turn' => (int)($data['current_turn'])]);
?>