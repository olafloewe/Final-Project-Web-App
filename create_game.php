<?php
function is_unique_code(string $game_code) : bool {
    include('db_credentials.php');

    $sql = "
    SELECT game_code
    FROM games
    WHERE game_code = :game_code AND is_in_progress = 1;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 0) return TRUE;
    return FALSE;
}

session_start();
include('db_credentials.php');

$user_id = $_SESSION['user_id'] ?? "";
if (empty($user_id)) {
    header("Location: login.php");
    exit;
}

$game_code = "";
do {
    $game_code = strtoupper(bin2hex(random_bytes(4)));
} while(!is_unique_code($game_code));

// query
$sql = "
    INSERT INTO games (game_code, player_one)
    VALUES (:game_code, :user_id)
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

$stmt->execute();

$_SESSION['game_code'] = $game_code;

header("Location: board.php");
exit();
?>
