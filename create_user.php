<?php
session_start();
include('db_credentials.php');

// data sent by user in form
$username = $_POST['username'];
$password = $_POST['password'];

// hash password using bcrypt
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql = "
    INSERT INTO users (username, password)
    VALUES (:username, :password)
";
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);

$stmt->execute();

$_SESSION['sign_up_message'] = 'Sign up was successful';
header('Location: login.php');
?>
