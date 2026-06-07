<?php
session_start();

$error_message = $_SESSION['code_error'] ?? '';
if (!empty($error_message)) unset($_SESSION['code_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/menu.css">
    <title>Menu</title>
</head>
<body>

    <div id="menu-container">
        <h1>WELCOME TO TIC-TAC-TOE</h1>

        <form action="create_game.php">
            <button id="create-game-btn">CREATE GAME</button>
        </form>

        <button id="join-game-btn" onclick="openJoinModal()">JOIN GAME</button>

        <button id="profile-btn" onclick="window.location.href='profile.php'">Profile</button>
    </div>

    <div id="join-modal-overlay">
        <div id="join-modal">
            <h2>JOIN GAME!</h2>
            <p>Please input the code below:</p>

            <form id="join-game-form" method="post" action="join_game.php" autocomplete="off">
                <input type="text" id="game-code-input" name="game_code" maxlength="8" required>

                <div id="game-code-error">
                    <?php if ($error_message): ?>
                        <p class="error-text">Invalid code!</p>
                    <?php endif; ?>
                </div>

                <button id="join-submit-btn" onclick="validateGameCode(event)">JOIN</button>
            </form>

            <button id="close-join-btn" onclick="closeJoinModal()">✕</button>
        </div>
    </div>

    <?php if ($error_message): ?>
    <script>
        // re-open modal if there was a code error on page load
        document.addEventListener('DOMContentLoaded', openJoinModal);
    </script>
    <?php endif; ?>

    <script src="js/menu.js"></script>
</body>
</html>
