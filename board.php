<?php
session_start();
include('api/db_credentials.php');

$game_id   = $_SESSION['game_id']   ?? '';
$game_code = $_SESSION['game_code'] ?? '';
$user_id   = $_SESSION['user_id']   ?? '';
$player_num = $_SESSION['player_num'] ?? '';

if (empty($game_id)) {
    header('Location: login.php');
    exit();
}

function get_game_status(int $game_id, $pdo): int {
    $sql  = 'SELECT game_status FROM games WHERE game_id = :game_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$data['game_status'];
}

$game_status = get_game_status($game_id, $pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/board.css">
    <title>Tic-Tac-Toe</title>
</head>
<body>

    <button id="back-to-menu-btn" onclick="window.location.href='menu.php'">← Menu</button>
    <button id="profile-btn" onclick="window.location.href='profile.php'">Profile</button>

    <div id="wait-message">
        <?php if ($game_status === 1): ?>
            <p>Waiting for other player...</p>
        <?php endif; ?>
    </div>

    <?php if ($game_status === 1): ?>
    <div id="invite-modal-overlay" class="show">
        <div id="invite-modal">
            <h2>INVITE YOUR FRIEND!</h2>
            <p>Share this code to play together:</p>
            <div id="code-container">
                <span id="game-code"><?php echo htmlspecialchars($game_code); ?></span>
                <button id="copy-code-btn" onclick="copyGameCode()">Copy</button>
            </div>
            <button id="close-invite-btn" onclick="closeInviteModal()">Got it!</button>
        </div>
    </div>
    <?php endif; ?>

    <div id="result-modal-overlay">
        <div id="result-modal">
            <div id="result-icon"></div>
            <h2 id="result-title"></h2>
            <p id="result-subtitle"></p>
            <button id="result-close-btn" onclick="closeResultModal()">Close</button>
            <button id="result-menu-btn" onclick="window.location.href='menu.php'">Main Menu</button>
        </div>
    </div>

    <div id="board"
        data-game-id="<?php echo (int)$game_id; ?>"
        data-user-id="<?php echo (int)$user_id; ?>"
        data-player-num="<?php echo (int)$player_num; ?>">
        <div class="cell" data-num="0"></div>
        <div class="cell" data-num="1"></div>
        <div class="cell" data-num="2"></div>
        <div class="cell" data-num="3"></div>
        <div class="cell" data-num="4"></div>
        <div class="cell" data-num="5"></div>
        <div class="cell" data-num="6"></div>
        <div class="cell" data-num="7"></div>
        <div class="cell" data-num="8"></div>
    </div>

    <script src="js/board.js"></script>
</body>
</html>
