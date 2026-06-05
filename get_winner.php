<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'];

// sql query to get winner
$sql = "
    SELECT winner
    FROM games 
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// fetch data
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Explicitly check for NULL - 0 is a valid winner value (tie)
$winner = isset($data['winner']) && $data['winner'] !== null ? (int)$data['winner'] : -1;

// return winner as int json
header('Content-Type: application/json');
echo json_encode(['winner' => $winner]);
?>