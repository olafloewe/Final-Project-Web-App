<?php
session_start();
// database login
include('db_credentials.php');

$user_id = $_SESSION['user_id'] ?? '';

// If the user is not logged in, return an empty history
if (empty($user_id)) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

// sql statement to fetch the match history for the logged-in user
$sql = '
    SELECT
        g.game_id,
        g.winner,
        g.player_one,
        g.player_two,
        u1.username AS player_one_name,
        u2.username AS player_two_name
    FROM games g
    JOIN users u1 ON g.player_one = u1.user_id
    JOIN users u2 ON g.player_two = u2.user_id
    WHERE
        (g.player_one = :user_id OR g.player_two = :user_id)
        AND g.game_status = 2
    ORDER BY g.game_id DESC
';

//  prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$games   = $stmt->fetchAll(PDO::FETCH_ASSOC);
$history = [];

// Process each game to determine the winner and the result for the logged-in user
foreach ($games as $game) {
    if ($game['winner'] === null) {
        $result      = 'Tie';
        $winner_name = '—';
    } elseif ((int)$game['winner'] === (int)$game['player_one']) {
        $winner_name = $game['player_one_name'];
        $result      = ((int)$game['winner'] === (int)$user_id) ? 'Win' : 'Loss';
    } else {
        $winner_name = $game['player_two_name'];
        $result      = ((int)$game['winner'] === (int)$user_id) ? 'Win' : 'Loss';
    }

    $history[] = [
        'game_id'         => $game['game_id'],
        'player_one_name' => $game['player_one_name'],
        'player_two_name' => $game['player_two_name'],
        'winner_name'     => $winner_name,
        'result'          => $result,
    ];
}

// Return the match history as JSON
header('Content-Type: application/json');
echo json_encode($history);
