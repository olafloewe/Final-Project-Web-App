<?php
session_start();
// database login
include('db_credentials.php');

$player_num = (int)($_GET['player_num'] ?? 1);

// check the player's symbol
$symbol = ($player_num === 1) ? 'X' : 'O';

// Return the player's symbol as JSON
header('Content-Type: application/json');
echo json_encode(['symbol' => $symbol]);
