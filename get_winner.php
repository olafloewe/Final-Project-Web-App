<?php
session_start();
// login to database
include('db_credentials.php');

$game_id = $_GET['game_id'];

// sql query to get winner
$sql = "
    SELECT winner, game_status 
    FROM games 
    WHERE game_id = :game_id;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

// fetch data
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data['winner'] !== null) {
    $winner = (int)$data['winner'];   // real winner user id
} elseif ((int)$data['game_status'] === 3) {
    $winner = 0;                       // tie - return 0
} else {
    $winner = -1;                      // game still in progress
}

// return winner as int json
header('Content-Type: application/json');
echo json_encode(['winner' => $winner]);
?>