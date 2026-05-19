<?php
    function get_player_id(string $game_code, int $num = 0) : int {
        include('db_credentials.php');

        $sql = "
            SELECT player_one, player_two FROM games
            WHERE game_code = :game_code AND is_in_progress = 1;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_code', $game_code, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $num === 0 ? (int)$data['player_one'] : (int)$data['player_two'];
    }
?>

<?php
session_start();

$game_code = $_SESSION['game_code'] ?? "";
$user_id = $_SESSION['user_id'] ?? "";

if (empty($game_code) || empty($user_id)) {
    header("Location: login.php");
    exit();
}
?>



<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="general_page_settings.css">
        <link rel="stylesheet" href="board.css">
        <title>Tic-Tac-Toe</title>

        <script>
            function copyCode() {
                $code = document.getElementById('game_code').textContent;
                navigator.clipboard.writeText($code).then(() => {
                    alert('Code copied to clipboard!');
                });
            }

            function closeModal() {
               document.getElementById('modal_container').classList.remove('show-modal');
            }
        </script>
    </head>

    <body>
        <div id="board">
            <div class="cell" data-cell="0"></div>
            <div class="cell" data-cell="1"></div>
            <div class="cell" data-cell="2"></div>
            <div class="cell" data-cell="3"></div>
            <div class="cell" data-cell="4"></div>
            <div class="cell" data-cell="5"></div>
            <div class="cell" data-cell="6"></div>
            <div class="cell" data-cell="7"></div>
            <div class="cell" data-cell="8"></div>
        </div>

        <script>
            $symbol = "O"
            $cells = document.querySelectorAll('.cell');
            $cells.forEach($cell => {
            $cell.addEventListener('click', function() {
                    if ($cell.innerHTML === "") {
                        if ($symbol === "O") {
                            $cell.innerHTML = '<img src="./images/blue_circle.png" width="60" height="auto">'
                            $cell.dataset.symbol = "O";
                            $symbol = "X"
                        }
                        else {
                            $cell.innerHTML = '<img src="./images/black_x.png" width="60" height="auto">'
                            $cell.dataset.symbol = "X";
                            $symbol = "O"
                        }
                    }
                });
            });
        </script>
        
        <?php
            if ($_SESSION['user_id'] === get_player_id($game_code)) { ?>
                <div id="modal_container" class="modal_container show-modal">
                    <div id="invite_modal">
                        <h2>INVITE YOUR FRIEND!</h2>
                        <p>Share this code to play together:</p>
                        <div id="code_container">
                            <span id="game_code"><?php echo $game_code ?></span>
                            <button class="copy_button" onclick="copyCode()">Copy</button>
                        </div>
                        <button class="close_button" onclick="closeModal()">Got it!</button>
                    </div>
                </div>
            <?php
            }
        ?>
    </body>
</html>

<?php
/*
$game_started = FALSE;

$player_one = get_player_id($game_code);
$player_two = get_player_id($game_code, 1);

// check whether player_two joined
while ($player_two === 0) {
    sleep(1);
    $player_two = get_player_id($game_code, 1);
}

$game_started = TRUE;*/
?>