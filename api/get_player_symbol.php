<?php
session_start();
include('db_credentials.php');

$player_num = (int)($_GET['player_num'] ?? 1);

$symbol = ($player_num === 1) ? 'X' : 'O';

header('Content-Type: application/json');
echo json_encode(['symbol' => $symbol]);
