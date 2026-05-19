<?php
/**
* login_check_credentials.php
* Receives POST: username, password
**/

// redirect back to the login page with the error message
function throw_login_error() {
    $_SESSION['login_error'] = 'Incorrect username or password!';
    header('Location: login.php');
    exit();
}

session_start();
include('db_credentials.php');

// data sent by user in form
$username = $_POST['username'];
$password = $_POST['password'];

// query to get the user info
$sql = "
    SELECT user_id, username, password, is_admin FROM users WHERE username = :username
";

// prepare query to avoid SQL injection
$stmt = $pdo->prepare($sql);

// put username into the variable
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

$stmt->execute();

// check whether such a username does not exist
if ($stmt->rowCount() === 0) throw_login_error();

// fetch record
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$hashed_password = $data['password'];
if (password_verify($password, $hashed_password)) {
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['username'] = $data['username']; 
    $_SESSION['isAdmin'] = $data['is_admin'];
    header("Location: menu.php");
    exit;
} else throw_login_error();
?>