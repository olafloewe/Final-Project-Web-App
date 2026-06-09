<?php
session_start();

$error_message = $_SESSION['login_error'] ?? "";
if (!empty($error_message)) unset($_SESSION['login_error']);

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

<!-- Tic-tac-toe animated background -->
<div class="bg-wrapper">

    <!-- MAIN BOARD — X wins diagonal top-left to bottom-right -->
    <div class="tic-tac-toe-bg main-board">
        <svg class="board-svg" viewBox="0 0 300 300">
            <line class="grid-line" x1="100" y1="10" x2="100" y2="290"/>
            <line class="grid-line" x1="200" y1="10" x2="200" y2="290"/>
            <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
            <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
            <g class="token delay-1">
                <line class="token-x-line" x1="25" y1="25" x2="75" y2="75"/>
                <line class="token-x-line" x1="75" y1="25" x2="25" y2="75"/>
            </g>
            <g class="token delay-2"><circle class="token-o" cx="150" cy="50" r="25"/></g>
            <g class="token delay-3">
                <line class="token-x-line" x1="125" y1="125" x2="175" y2="175"/>
                <line class="token-x-line" x1="175" y1="125" x2="125" y2="175"/>
            </g>
            <g class="token delay-4"><circle class="token-o" cx="50" cy="150" r="25"/></g>
            <g class="token delay-5">
                <line class="token-x-line" x1="225" y1="225" x2="275" y2="275"/>
                <line class="token-x-line" x1="275" y1="225" x2="225" y2="275"/>
            </g>
            <line class="win-line win-x" x1="25" y1="25" x2="275" y2="275"/>
        </svg>
    </div>

    <!-- TOP-LEFT BOARD — Draw -->
    <div class="tic-tac-toe-bg mini-board top-left">
        <svg class="board-svg" viewBox="0 0 300 300">
            <line class="grid-line" x1="100" y1="10" x2="100" y2="290"/>
            <line class="grid-line" x1="200" y1="10" x2="200" y2="290"/>
            <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
            <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
            <g class="token delay-1">
                <line class="token-x-line" x1="25" y1="25" x2="75" y2="75"/>
                <line class="token-x-line" x1="75" y1="25" x2="25" y2="75"/>
            </g>
            <g class="token delay-2"><circle class="token-o" cx="150" cy="50" r="25"/></g>
            <g class="token delay-3">
                <line class="token-x-line" x1="225" y1="25" x2="275" y2="75"/>
                <line class="token-x-line" x1="275" y1="25" x2="225" y2="75"/>
            </g>
            <g class="token delay-4"><circle class="token-o" cx="150" cy="150" r="25"/></g>
            <g class="token delay-5">
                <line class="token-x-line" x1="25" y1="125" x2="75" y2="175"/>
                <line class="token-x-line" x1="75" y1="125" x2="25" y2="175"/>
            </g>
            <g class="token delay-6"><circle class="token-o" cx="250" cy="150" r="25"/></g>
            <g class="token delay-7">
                <line class="token-x-line" x1="125" y1="225" x2="175" y2="275"/>
                <line class="token-x-line" x1="175" y1="225" x2="125" y2="275"/>
            </g>
            <g class="token delay-8"><circle class="token-o" cx="50" cy="250" r="25"/></g>
            <g class="token delay-9">
                <line class="token-x-line" x1="225" y1="225" x2="275" y2="275"/>
                <line class="token-x-line" x1="275" y1="225" x2="225" y2="275"/>
            </g>
        </svg>
    </div>

    <!-- BOTTOM-RIGHT BOARD — O wins top row -->
    <div class="tic-tac-toe-bg mini-board bottom-right">
        <svg class="board-svg" viewBox="0 0 300 300">
            <line class="grid-line" x1="100" y1="10" x2="100" y2="290"/>
            <line class="grid-line" x1="200" y1="10" x2="200" y2="290"/>
            <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
            <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
            <g class="token delay-1">
                <line class="token-x-line" x1="125" y1="125" x2="175" y2="175"/>
                <line class="token-x-line" x1="175" y1="125" x2="125" y2="175"/>
            </g>
            <g class="token delay-2"><circle class="token-o" cx="50"  cy="50" r="25"/></g>
            <g class="token delay-3">
                <line class="token-x-line" x1="25" y1="225" x2="75" y2="275"/>
                <line class="token-x-line" x1="75" y1="225" x2="25" y2="275"/>
            </g>
            <g class="token delay-4"><circle class="token-o" cx="150" cy="50" r="25"/></g>
            <g class="token delay-5">
                <line class="token-x-line" x1="125" y1="225" x2="175" y2="275"/>
                <line class="token-x-line" x1="175" y1="225" x2="125" y2="275"/>
            </g>
            <g class="token delay-6"><circle class="token-o" cx="250" cy="50" r="25"/></g>
            <line class="win-line win-o" x1="25" y1="50" x2="275" y2="50"/>
        </svg>
    </div>

</div>

<!-- Login card -->
<div id="login-container">
    <h1>LOGIN</h1>

    <?php if ($sign_up_message): ?>
        <p id="signup-success-msg">The sign up was successful!<br>Please login below</p>
    <?php endif; ?>

    <form id="login-form" method="post" action="login_check_credentials.php" autocomplete="off">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <?php if ($error_message): ?>
            <p id="login-error-msg">Incorrect username or password!</p>
        <?php endif; ?>

        <button type="button" id="login-btn" onclick="this.closest('form').submit()">LOGIN</button>
    </form>

    <div id="signup-link">
        <p>Don't have an account?</p>
        <a href="sign_up.html">Sign up!</a>
    </div>
</div>

</body>
</html>