<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'];

// sql query to get game state
$sql = "
    SELECT game_state
    FROM games 
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// return game state as json
$data = $stmt->fetch(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode(['game_state' => $data['game_state']]);
?>