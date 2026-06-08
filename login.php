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
        <div class="bg-wrapper">

            <!-- MAIN BOARD — X wins diagonal top-left to bottom-right -->
            <div class="tic-tac-toe-bg main-board">
                <svg class="board-svg" viewBox="0 0 300 300">
                    <!-- Grid -->
                    <line class="grid-line" x1="100" y1="10"  x2="100" y2="290"/>
                    <line class="grid-line" x1="200" y1="10"  x2="200" y2="290"/>
                    <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
                    <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
                    <!-- X (0,0) delay-1 -->
                    <g class="token delay-1">
                        <line class="token-x-line" x1="25" y1="25" x2="75" y2="75"/>
                        <line class="token-x-line" x1="75" y1="25" x2="25" y2="75"/>
                    </g>
                    <!-- O (0,1) delay-2 -->
                    <g class="token delay-2">
                        <circle class="token-o" cx="150" cy="50" r="25"/>
                    </g>
                    <!-- X (1,1) delay-3 -->
                    <g class="token delay-3">
                        <line class="token-x-line" x1="125" y1="125" x2="175" y2="175"/>
                        <line class="token-x-line" x1="175" y1="125" x2="125" y2="175"/>
                    </g>
                    <!-- O (1,0) delay-4 -->
                    <g class="token delay-4">
                        <circle class="token-o" cx="50" cy="150" r="25"/>
                    </g>
                    <!-- X (2,2) delay-5 -->
                    <g class="token delay-5">
                        <line class="token-x-line" x1="225" y1="225" x2="275" y2="275"/>
                        <line class="token-x-line" x1="275" y1="225" x2="225" y2="275"/>
                    </g>
                    <!-- Winning line X diagonal -->
                    <line class="win-line win-x" x1="25" y1="25" x2="275" y2="275"/>
                </svg>
            </div>

            <!-- TOP-LEFT BOARD — Draw (full board, no winner) -->
            <div class="tic-tac-toe-bg mini-board top-left">
                <svg class="board-svg" viewBox="0 0 300 300">
                    <line class="grid-line" x1="100" y1="10"  x2="100" y2="290"/>
                    <line class="grid-line" x1="200" y1="10"  x2="200" y2="290"/>
                    <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
                    <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
                    <!-- X (0,0) -->
                    <g class="token delay-1">
                        <line class="token-x-line" x1="25" y1="25" x2="75" y2="75"/>
                        <line class="token-x-line" x1="75" y1="25" x2="25" y2="75"/>
                    </g>
                    <!-- O (0,1) -->
                    <g class="token delay-2">
                        <circle class="token-o" cx="150" cy="50" r="25"/>
                    </g>
                    <!-- X (0,2) -->
                    <g class="token delay-3">
                        <line class="token-x-line" x1="225" y1="25" x2="275" y2="75"/>
                        <line class="token-x-line" x1="275" y1="25" x2="225" y2="75"/>
                    </g>
                    <!-- O (1,1) -->
                    <g class="token delay-4">
                        <circle class="token-o" cx="150" cy="150" r="25"/>
                    </g>
                    <!-- X (1,0) -->
                    <g class="token delay-5">
                        <line class="token-x-line" x1="25" y1="125" x2="75" y2="175"/>
                        <line class="token-x-line" x1="75" y1="125" x2="25" y2="175"/>
                    </g>
                    <!-- O (1,2) -->
                    <g class="token delay-6">
                        <circle class="token-o" cx="250" cy="150" r="25"/>
                    </g>
                    <!-- X (2,1) -->
                    <g class="token delay-7">
                        <line class="token-x-line" x1="125" y1="225" x2="175" y2="275"/>
                        <line class="token-x-line" x1="175" y1="225" x2="125" y2="275"/>
                    </g>
                    <!-- O (2,0) -->
                    <g class="token delay-8">
                        <circle class="token-o" cx="50" cy="250" r="25"/>
                    </g>
                    <!-- X (2,2) -->
                    <g class="token delay-9">
                        <line class="token-x-line" x1="225" y1="225" x2="275" y2="275"/>
                        <line class="token-x-line" x1="275" y1="225" x2="225" y2="275"/>
                    </g>
                    <!-- No win line — draw -->
                </svg>
            </div>

            <!-- BOTTOM-RIGHT BOARD — O wins top row -->
            <div class="tic-tac-toe-bg mini-board bottom-right">
                <svg class="board-svg" viewBox="0 0 300 300">
                    <line class="grid-line" x1="100" y1="10"  x2="100" y2="290"/>
                    <line class="grid-line" x1="200" y1="10"  x2="200" y2="290"/>
                    <line class="grid-line" x1="10"  y1="100" x2="290" y2="100"/>
                    <line class="grid-line" x1="10"  y1="200" x2="290" y2="200"/>
                    <!-- X (1,1) delay-1 -->
                    <g class="token delay-1">
                        <line class="token-x-line" x1="125" y1="125" x2="175" y2="175"/>
                        <line class="token-x-line" x1="175" y1="125" x2="125" y2="175"/>
                    </g>
                    <!-- O (0,0) delay-2 -->
                    <g class="token delay-2">
                        <circle class="token-o" cx="50" cy="50" r="25"/>
                    </g>
                    <!-- X (2,0) delay-3 -->
                    <g class="token delay-3">
                        <line class="token-x-line" x1="25" y1="225" x2="75" y2="275"/>
                        <line class="token-x-line" x1="75" y1="225" x2="25" y2="275"/>
                    </g>
                    <!-- O (0,1) delay-4 -->
                    <g class="token delay-4">
                        <circle class="token-o" cx="150" cy="50" r="25"/>
                    </g>
                    <!-- X (2,1) delay-5 -->
                    <g class="token delay-5">
                        <line class="token-x-line" x1="125" y1="225" x2="175" y2="275"/>
                        <line class="token-x-line" x1="175" y1="225" x2="125" y2="275"/>
                    </g>
                    <!-- O (0,2) delay-6 -->
                    <g class="token delay-6">
                        <circle class="token-o" cx="250" cy="50" r="25"/>
                    </g>
                    <!-- Winning line O top row -->
                    <line class="win-line win-o" x1="25" y1="50" x2="275" y2="50"/>
                </svg>
            </div>

        </div>

        <div class="container" id="login_form">
            <h1>LOGIN</h1>
            <?php if ($sign_up_message): ?>
                <p class="msg-box msg-success">The sign up was successful!<br>Please login below</p>
            <?php endif; ?>
            <form method="post" action="login_check_credentials.php" autocomplete="off">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <?php if ($error_message): ?>
                    <p class="msg-box msg-error">Incorrect username or password!</p>
                <?php endif; ?>
                <button type="submit">LOGIN</button>
            </form>
            <div id="sign_up">
                <p>Don't have an account?</p>
                <a href="sign_up.html">Sign up!</a>
            </div>
        </div>
    </body>
</html>
