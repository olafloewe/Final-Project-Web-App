<?php
$db_host = 'localhost';
$db_name = 'prestashop_pzx124573'; 
$db_user = 'pzx124573';     
$db_password = 'pWSrTLS5neWuey5y'; 

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

$pdo = new PDO($dsn, $db_user, $db_password);
?>