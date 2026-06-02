<?php
session_start();

// check for error message
$error_message = $_SESSION['login_error'] ?? "";

if (!empty($error_message)) unset($_SESSION['login_error']);

// check for successful sign up message
$sign_up_message = $_SESSION['sign_up_message'] ?? "";

if (!empty($sign_up_message)) unset($_SESSION['sign_up_message']);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="general_page_settings.css">
        <link rel="stylesheet" href="login.css">
        <title>Login</title>    
    </head>
      
    <body>
        <div class="bg-wrapper">
            <div class="tic-tac-toe-bg main-board">
                <div class="cell x token-1"></div><div class="cell o token-4"></div><div class="cell x token-5"></div>
                <div class="cell o token-2"></div><div class="cell x token-3"></div><div class="cell"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell o token-6"></div>
            </div>

            <div class="tic-tac-toe-bg mini-board top-left">
                <div class="cell x token-3"></div><div class="cell"></div><div class="cell o token-1"></div>
                <div class="cell"></div><div class="cell x token-2"></div><div class="cell"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell o token-4"></div>
            </div>

            <div class="tic-tac-toe-bg mini-board bottom-right">
                <div class="cell o token-2"></div><div class="cell"></div><div class="cell"></div>
                <div class="cell x token-1"></div><div class="cell x token-3"></div><div class="cell o token-4"></div>
                <div class="cell"></div><div class="cell"></div><div class="cell"></div>
            </div>
        </div>

        <div class="container" id="login_form">
            <h1>LOGIN</h1>
            
            <?php
                if ($sign_up_message) {
                    echo '<p class="msg-box msg-success">The sign up was successful!<br>Please login below</p>';
                }
            ?>
            
            <form method="post" action="login_check_credentials.php" autocomplete="off">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <?php
                    if ($error_message) {
                        echo '<p class="msg-box msg-error">Incorrect username or password!</p>';
                    }
                ?>
                
                <button type="submit">LOGIN</button>
            </form>

            <div id="sign_up">
                <p>Don't have an account?</p>
                <a href="sign_up.html">Sign up!</a>
            </div>
        </div>
    </body>
</html>