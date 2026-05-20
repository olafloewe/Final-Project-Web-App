<?php
session_start();
include('db_credentials.php');

$game_code = $_GET['game_code'];

$sql = "
    SELECT game_status
    FROM games 
    WHERE game_code = :game_code AND game_status != 0;
    ";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['game_status' => (int)($data['game_status'])]);
?>