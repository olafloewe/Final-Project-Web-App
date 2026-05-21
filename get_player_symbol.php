<?php
session_start();

$player_num = $_GET['player_num'] ?? '';

$symbol = $player_num === '0' ? "X" : "O";

header('Content-Type: application/json');
echo json_encode(['symbol' => $symbol]);
?>