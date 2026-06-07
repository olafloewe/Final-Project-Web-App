<?php
session_start();
include('db_credentials.php');

$user_id = $_SESSION['user_id'] ?? '';

if (empty($user_id)) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

$sql = 'SELECT username FROM users WHERE user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = '
    SELECT
        COUNT(*) AS games_played,
        SUM(CASE WHEN winner = :user_id THEN 1 ELSE 0 END) AS games_won
    FROM games
    WHERE player_one = :user_id OR player_two = :user_id
';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$games_played = (int)$stats['games_played'];
$games_won    = (int)$stats['games_won'];
$games_lost   = $games_played - $games_won;
$win_rate     = $games_played > 0 ? round(($games_won / $games_played) * 100, 1) : 0;

header('Content-Type: application/json');
echo json_encode([
    'username'     => $user['username'],
    'user_id'      => (int)$user_id,
    'games_played' => $games_played,
    'games_won'    => $games_won,
    'games_lost'   => $games_lost,
    'win_rate'     => $win_rate,
]);
