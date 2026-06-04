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
// login to database
include('db_credentials.php');

// data sent by user in form
$username = $_POST['username'];
$password = $_POST['password'];

// SQL query to get user record from database
$sql = "
    SELECT user_id, username, password, is_admin FROM users WHERE username = :username
";

// prepare and execute SQL with injection protection
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->execute();

// check if username exists else return error
if ($stmt->rowCount() === 0) throw_login_error();

// fetch record
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// verify password
$hashed_password = $data['password'];
if (password_verify($password, $hashed_password)) {
    // set session variables and redirect to menu
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['username'] = $data['username']; 
    $_SESSION['isAdmin'] = $data['is_admin'];
    header("Location: menu.php");
    exit;
} else {
    // throw error if password is incorrect
    throw_login_error();
}
?>