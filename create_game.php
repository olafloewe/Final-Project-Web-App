<?php
// function to check if game code is unique (not already in use for an active game)
function is_unique_code(string $game_code) : bool {
    // login to database
    include('api/db_credentials.php');

    // sql query to check if game code already exists for an active game
    $sql = "
    SELECT game_code
    FROM games
    WHERE game_code = :game_code AND game_status != 0;
    ";

    // prepare and execute SQL with injection protection
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
    $stmt->execute();

    // return wether game code is unique
    if ($stmt->rowCount() === 0) return TRUE;
    return FALSE;
}

session_start();
// login to database
include('api/db_credentials.php');

// check for user id else redirect to login page
$user_id = $_SESSION['user_id'] ?? "";
if (empty($user_id)) {
    header("Location: login.php");
    exit;
}

// generate unique game code
$game_code = "";
do {
    $game_code = strtoupper(bin2hex(random_bytes(4)));
} while(!is_unique_code($game_code));

// sql query to insert new game
$sql = "
    INSERT INTO games (game_code, player_one)
    VALUES (:game_code, :user_id)
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();

// get game id of newly created game
$game_id = $pdo->lastInsertId();

// set session variables
$_SESSION['player_num'] = '0';
$_SESSION['game_code'] = $game_code;
$_SESSION['game_id'] = (int)$game_id;

// redirect to game board
header("Location: board.php");
exit();
?>