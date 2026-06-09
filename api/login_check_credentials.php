<?php
session_start();
// database login
include('db_credentials.php');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = '
    SELECT user_id, password 
    FROM users 
    WHERE username = :username
';

// prepare and execute the statement with injection prevention
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify the password and set session variables accordingly
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    header('Location: ../menu.php');
} else {
    $_SESSION['login_error'] = true;
    header('Location: ../login.php');
}

exit();
