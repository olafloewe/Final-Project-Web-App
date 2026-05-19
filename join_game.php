<?php
function code_exists(string $game_code) : bool {
    include('db_credentials.php');

    $sql = "
    SELECT game_code
    FROM games
    WHERE game_code = :game_code AND game_status= 1 AND player_two IS NULL;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 1) return TRUE;
    return FALSE;
}

session_start();
include('db_credentials.php');
$game_code = $_POST['game_code'];

if (!code_exists($game_code)) {
    $_SESSION['code_error'] = 'Invalid code!';
    header('Location: menu.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
UPDATE games
SET player_two = :user_id
WHERE game_code = :game_code;
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);

$stmt->execute();

header('Location: board.php');
exit();
?>