<?php
session_start();
// database login
include('db_credentials.php');

$game_id = $_GET['game_id'] ?? '';

// sql statement to fetch the winner and game status for the specified game
$sql = '
    SELECT winner, game_status, player_one, player_two 
    FROM games 
    WHERE game_id = :game_id
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Determine the winner based on the stored user_id and game status
if ($data['winner'] !== null) {
    // translate stored user_id back to player number (0 or 1)
    $result = ((int)$data['winner'] === (int)$data['player_one']) ? 0 : 1;
} elseif ((int)$data['game_status'] === 3) {
    $result = -2; // tie
} else {
    $result = -1; // game still in progress
}

// Return the winner information as JSON
header('Content-Type: application/json');
echo json_encode(['winner' => $result]);