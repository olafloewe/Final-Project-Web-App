<?php
session_start();

$player_num = $_GET['player_num'] ?? '';

// determine symbol based on player number
$symbol = $player_num === '0' ? "X" : "O";

// return symbol as json
header('Content-Type: application/json');
echo json_encode(['symbol' => $symbol]);
?>