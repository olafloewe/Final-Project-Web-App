<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';
$game_state = $_GET['game_state'] ?? '';

// sql query to update game state
$sql = "
    UPDATE games
    SET game_state = :game_state
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_state', $game_state, PDO::PARAM_STR);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return game state as json
$data = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode(['game_state' => $data['game_state']]);
?>