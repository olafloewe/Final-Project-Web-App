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

function get_game_id(string $game_code) : int {
    include('db_credentials.php');

    $sql = "
    SELECT game_id
    FROM games
    WHERE game_code = :game_code AND game_status != 0;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$data['game_id'];
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

$sql = "
    UPDATE games
    SET game_status = 2
    WHERE game_code = :game_code;
";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);

$stmt->execute();

$game_id = $pdo->lastInsertId();

$_SESSION['player_num'] = '1';
$_SESSION['game_code'] = $game_code;
$_SESSION['game_id'] = get_game_id($game_code);

header('Location: board.php');
exit();
?>