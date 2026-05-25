<?php
    function get_player_id(int $game_id, int $num = 0) : int {
        include('db_credentials.php');

        $sql = "
            SELECT player_one, player_two 
            FROM games
            WHERE game_id = :game_id;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $num === 0 ? (int)$data['player_one'] : (int)$data['player_two'];
    }

    function get_game_status(int $game_id) : int {
        include('db_credentials.php');

        $sql = "
            SELECT game_status 
            FROM games
            WHERE game_id = :game_id;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$data['game_status'];
    }

    function get_player_turn(int $game_id) {
        include('db_credentials.php');

        $sql = "
            SELECT current_turn 
            FROM games
            WHERE game_id = :game_id;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $current_turn = $data['current_turn'] ?? '';

        if (empty(current_turn)) {
            return set_current_turn($game_id, $current_turn);
        }

        return $current_turn;
    }
?>

<?php
session_start();

$game_id = $_SESSION['game_id'] ?? "";
$game_code = $_SESSION['game_code'] ?? "";
$user_id = $_SESSION['user_id'] ?? "";
$player_num = $_SESSION['player_num'] ?? "";

echo($game_id);

if (empty($game_id)) {
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

            function gameActive() {
                fetch('check_game_status.php?game_id=<?php echo $game_id; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.game_status === 2) {
                        document.getElementById('wait-message').innerHTML = "";
                        startGame();
                    }
                    else setTimeout(gameActive, 2000);
                });
            }

            function getPlayerSymbol() {
                return fetch('get_player_symbol.php?player_num=<?php echo $player_num; ?>')
                .then(response => response.json())
                .then(data => data.symbol);
            }

            function waitForTurn() {
                fetch('get_player_turn.php?game_id=<?php echo $game_id; ?>')
                .then(response => response.json())
                .then(data => {
                    let playerNum = <?php echo $player_num; ?>
                    //let currentTurn = data.current_turn; //this
                    /*if (playerNum === currentTurn) {
                        console.log(`Turn: ${playerNum}`);
                    } else {
                        console.log('not your turn');
                        setTimeout(waitForTurn, 2000);
                    }*/
                })
            }

            function startGame() {
            getPlayerSymbol()
            .then(symbol => {
                let playerSymbol = symbol;
                fetch('set_player_turn.php?game_id=<?php echo $game_id; ?>&current_turn=-1')
                .then(() => {
                    waitForTurn();
                });
            });
        }
            
        </script>
    </head>

    <body>
        <div class="message" id="wait-message"></div>
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
            if (get_game_status($game_id) === 1) { ?>
                <script>
                    document.getElementById('wait-message').innerHTML = "<p>Waiting for other player to join...</p>";
                </script>

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

        <script>
            gameActive();
        </script>
    </body>
</html>