<?php
session_start();

$error_message   = $_SESSION['login_error']    ?? '';
$sign_up_message = $_SESSION['sign_up_message'] ?? '';

if (!empty($error_message))   unset($_SESSION['login_error']);
if (!empty($sign_up_message)) unset($_SESSION['sign_up_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>
<body>

    <div id="login-container">
        <h1>LOGIN</h1>

        <?php if ($sign_up_message): ?>
            <p id="signup-success-msg">Sign up successful! Please log in below.</p>
        <?php endif; ?>

        <form id="login-form" method="post" action="api/login_check_credentials.php" autocomplete="off">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <?php if ($error_message): ?>
                <p id="login-error-msg">Incorrect username or password!</p>
            <?php endif; ?>

            <button id="login-btn" type="submit">LOGIN</button>
        </form>

        <div id="signup-link">
            <p>Don't have an account?</p>
            <a href="sign_up.html">Sign up!</a>
        </div>
    </div>

</body>
</html>
