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

        if (empty($current_turn)) {
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
               document.getElementById('modal_container').classList.remove('show');
            }
            
            function enableBoard() {
                cells.forEach(cell => {
                    cell.classList.add('show');
                })
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

            function changePlayerTurn() {
                return new Promise(resolve => {
                    fetch(`set_player_turn.php?game_id=<?php echo $game_id; ?>&current_turn=${currentTurn}`)
                    .then(() => {
                        console.log('ok')
                        resolve(true);
                    })
                }) 
            }

            function setGameState() {
                return new Promise(resolve => {
                    fetch(`set_game_state.php?game_id=<?php echo $game_id; ?>&game_state=${gameState}`)
                    .then(() => {
                        console.log('new game state update');
                        resolve(true);
                    })
                })
            }

            function updateBoardView() {
                return new Promise(resolve => {
                    cells.forEach(cell => {
                        if (gameState[cell.dataset.num] === 'X') {
                            cell.innerHTML = '<img src="./images/black_x.png" width="60" height="auto">';
                        } else if (gameState[cell.dataset.num] === 'O') {
                            cell.innerHTML = '<img src="./images/blue_circle.png" width="60" height="auto">';
                        }
                    });
                    resolve(true);
                })
                
            }

            async function updateGameState() {
                return new Promise(resolve => {
                    fetch('get_game_state.php?game_id=<?php echo $game_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        gameState = data.game_state;
                        newGameState = gameState.split('');
                        resolve(true);
                    });
                })
            }

            async function madeMove() {
                while (gameState === newGameState.join('')) {
                    await new Promise(resolve => {
                        console.log('no move');
                        setTimeout(resolve, 100);
                    })
                }
                console.log('move');
                isPlayerTurn = false;
                gameState = newGameState.join('');
                await setGameState();
                await checkForWinner();
            }

            function setWinner() {
                return new Promise(resolve => {
                    fetch(`set_winner.php?game_id=<?php echo $game_id; ?>&winner=${playerNum}`)
                    .then(response => response.json())
                    .then(data => {
                        winner = data.winner;
                        console.log(`the winner is: ${winner}`);
                        resolve(true);
                    })
                })
            }

            function getWinner() {
                return new Promise(resolve => {
                    fetch('get_winner.php?game_id=<?php echo $game_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.winner);
                        winner = data.winner;
                        resolve(true);
                    });
                }) 
            }

            function checkForWinner() {
                return new Promise(async(resolve) => {

                    // check whether player won by filling a row
                    for (let i = 0; i <= 6; i += 3) {
                        if (newGameState[i] === playerSymbol && newGameState[i + 1] === playerSymbol && newGameState[i + 2] === playerSymbol) {
                            setWinner();
                        }
                    }

                    // check whether player won by filling column
                    for (let i = 0; i <= 2; i++) {
                        if (newGameState[i] === playerSymbol && newGameState[i + 3] === playerSymbol && newGameState[i + 6] === playerSymbol) {
                            setWinner();
                        }
                    }

                    // check whether player won by filling the spaces across the board
                    let dif = 4; 
                    for (let i = 0; i <= 2; i += 2) //"Checking if X or O won by filling the spaces across"
                    {
                        if (newGameState[i] === playerSymbol && newGameState[i + dif] === playerSymbol && newGameState[i + 2 * dif] === playerSymbol) {
                            setWinner();
                        }
                        dif = 2;
                    }
                    resolve(true);
                })
            }

            async function waitForTurn() {
                return new Promise(async(resolve) => {
                    let response = await fetch('get_player_turn.php?game_id=<?php echo $game_id; ?>');
                    let data = await response.json();
                    currentTurn = data.current_turn;

                    if (currentTurn === playerNum) {
                        await getWinner();
                        if (winner !== -1) {
                            resolve('exit');
                        }
                        
                        await updateGameState();
                        await updateBoardView();
                        
                        console.log('your turn')
                        isPlayerTurn = true;
                        resolve(true);
                    } else {
                        console.log('not your turn')
                        setTimeout(() => {
                            waitForTurn()
                            .then(resolve);  
                        }, 2000);
                    }
                })
            }


            async function game() {
                enableBoard();

                let exit = false;
                for(let i = 0; i < 9; i++) {
                    let result = await waitForTurn();
                    if (result === 'exit') exit = true;
                    console.log('wait')

                    await madeMove();
                    if (winner !== -1) exit = true;

                    console.log('move');
                    await changePlayerTurn();

                    
                    fetch('get_game_state.php?game_id=<?php echo $game_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.game_state)
                    })
                  
                }
            }

            function startGame() {
                getPlayerSymbol()
                .then(symbol => {
                    playerSymbol = symbol;
                    fetch(`set_player_turn.php?game_id=<?php echo $game_id; ?>&current_turn=${currentTurn}`)
                    .then(() => game());
                });
            }
            
        </script>
    </head>

    <body>
        <div class="message" id="wait-message"></div>
        <div id="board">
            <div class="cell" data-num ="0"></div>
            <div class="cell" data-num ="1"></div>
            <div class="cell" data-num ="2"></div>
            <div class="cell" data-num ="3"></div>
            <div class="cell" data-num ="4"></div>
            <div class="cell" data-num ="5"></div>
            <div class="cell" data-num ="6"></div>
            <div class="cell" data-num ="7"></div>
            <div class="cell" data-num ="8"></div>
        </div>
        
        <?php
            if (get_game_status($game_id) === 1) { ?>
                <script>
                    document.getElementById('wait-message').innerHTML = "<p>Waiting for other player to join...</p>";
                </script>

                <div id="modal_container" class="modal_container show">
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
            let playerNum = <?php echo $player_num; ?>;
            let isPlayerTurn = false;
            let playerSymbol = '';
            let currentTurn = -1;
            let winner = -1;
            let gameState = '---------';
            let newGameState = gameState.split('');

            console.log(gameState)

            let cells = document.querySelectorAll('.cell');

            cells.forEach(cell => {
                cell.addEventListener('click', function() {
                    if (!isPlayerTurn) return;
                    
                    console.log(cell.dataset.num)

                    if (cell.innerHTML === "") {
                        if (playerSymbol === "O") {
                            cell.innerHTML = '<img src="./images/blue_circle.png" width="60" height="auto">'
                            
                        }
                        else {
                            cell.innerHTML = '<img src="./images/black_x.png" width="60" height="auto">'
                        }

                        // update game state
                        newGameState[cell.dataset.num] = playerSymbol;
                        console.log(newGameState.join(''));
                    }
                });
            });
        </script>

        <script>
            gameActive();
        </script>
    </body>
</html>