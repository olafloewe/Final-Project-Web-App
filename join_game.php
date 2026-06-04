<?php
// function to check if game code exists for an active game with only one player
function code_exists(string $game_code) : bool {
    // login to database
    include('db_credentials.php');

    // sql query to check if game code exists for an active game with only one player
    $sql = "
    SELECT game_code
    FROM games
    WHERE game_code = :game_code AND game_status= 1 AND player_two IS NULL;
    ";

    // prepare and execute SQL with injection protection
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    // return wether game code exists
    if ($stmt->rowCount() === 1) return TRUE;
    return FALSE;
}

// function to get game id from game code
function get_game_id(string $game_code) : int {
    // login to database
    include('db_credentials.php');

    // sql query to get game id from game code
    $sql = "
    SELECT game_id
    FROM games
    WHERE game_code = :game_code AND game_status != 0;
    ";

    // prepare and execute SQL with injection protection
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    // fetch data and return game id
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$data['game_id'];
}

session_start();

// login to database
include('db_credentials.php');
$game_code = $_POST['game_code'];

// check if game code exists for an active game with only one player else return error
if (!code_exists($game_code)) {
    $_SESSION['code_error'] = 'Invalid code!';
    header('Location: menu.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// sql query to update game with player two
$sql = "
    UPDATE games
    SET player_two = :user_id
    WHERE game_code = :game_code;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);

$stmt->execute();

// sql query to update game status to active
$sql = "
    UPDATE games
    SET game_status = 2
    WHERE game_code = :game_code;
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->execute();

// get game id of newly joined game
$game_id = $pdo->lastInsertId();

// set session variables
$_SESSION['player_num'] = '1';
$_SESSION['game_code'] = $game_code;
$_SESSION['game_id'] = get_game_id($game_code);

// redirect to game board
header('Location: board.php');
exit();
?>