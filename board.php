<?php
    // get player id of player one or two depending on num parameter
    function get_player_id(int $game_id, int $num = 0) : int {
        // login to database
        include('db_credentials.php');

        // sql statement to get player ids of player one and two
        $sql = "
            SELECT player_one, player_two 
            FROM games
            WHERE game_id = :game_id;
        ";

        // prepare and execute sql with injection protection
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        // fetch and save data
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // ternary operator to return player or player two depending on parameter
        return $num === 0 ? (int)$data['player_one'] : (int)$data['player_two'];
    }

    // query to get game status of game with given id
    function get_game_status(int $game_id) : int {
        // login to database
        include('db_credentials.php');

        // sql statement to get game status of game with passed id
        $sql = "
            SELECT game_status 
            FROM games
            WHERE game_id = :game_id;
        ";

        // prepare and execute sql with injection protection
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        // fetch and save data
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // return game status as integer
        return (int)$data['game_status'];
    }

    // query to get current player turn of game with given id
    function get_player_turn(int $game_id) {
        // login to database
        include('db_credentials.php');

        // sql statement to get current player turn of game with passed id
        $sql = "
            SELECT current_turn 
            FROM games
            WHERE game_id = :game_id;
        ";

        // prepare and execute sql with injection protection
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->execute();

        // fetch and save data
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // save current player turn with null coalescing
        $current_turn = $data['current_turn'] ?? '';

        // no ones turn 
        if (empty($current_turn)) {
            // set it to player one's turn and return it
            return set_current_turn($game_id, $current_turn);
        }

        // else return current player turn
        return $current_turn;
    }
?>

<?php
session_start();

// save game id, game code, user id and player num from session with null coalescing
$game_id = $_SESSION['game_id'] ?? "";
$game_code = $_SESSION['game_code'] ?? "";
$user_id = $_SESSION['user_id'] ?? "";
$player_num = $_SESSION['player_num'] ?? "";

// game id null failsafe, redirect to login page
if (empty($game_id)) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- link stylesheets and set title -->
        <link rel="stylesheet" href="general_page_settings.css">
        <link rel="stylesheet" href="board.css">
        <title>Tic-Tac-Toe</title>

        <script>
            // function to copy game code to clipboard
            function copyCode() {
                $code = document.getElementById('game_code').textContent;
                navigator.clipboard.writeText($code).then(() => {
                    alert('Code copied to clipboard!');
                });
            }

            // function to close the invite modal
            function closeModal() {
               document.getElementById('modal_container').classList.remove('show');
            }
            
            // show the board to allow players to make moves
            function enableBoard() {
                cells.forEach(cell => { 
                    cell.classList.add('show');
                })
            }    

            // check wether game is active every 2 seconds and start game when it is
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

            // get player symbol of player depending on num parameter
            function getPlayerSymbol() {
                return fetch('get_player_symbol.php?player_num=<?php echo $player_num; ?>')
                .then(response => response.json())
                .then(data => data.symbol);
            }

            // change player turn in database to the other player
            function changePlayerTurn() {
                return new Promise(resolve => {
                    fetch(`set_player_turn.php?game_id=<?php echo $game_id; ?>&current_turn=${currentTurn}`)
                    .then(() => {
                        console.log('ok')
                        resolve(true);
                    })
                }) 
            }

            // set game state in database to the current game state
            function setGameState() {
                return new Promise(resolve => {
                    fetch(`set_game_state.php?game_id=<?php echo $game_id; ?>&game_state=${gameState}`)
                    .then(() => {
                        console.log('new game state update');
                        resolve(true);
                    })
                })
            }

            // update the board view to match the current game state
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

            // update the game state by fetching it from the database and splitting it into an array
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

            // wait till player makes a move based on if gamestate has been changed
            // when move is made update game state, change player turn and check for winner  
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

            // save the winning player to the database and return it
            function setWinner() {
                return new Promise(resolve => {
                    fetch(`set_winner.php?game_id=${gameId}&winner=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            winner = data.winner;
                            console.log(`Winner: Player ${winner}`);
                            resolve(true);
                        });
                });
            }

            function setTie() {
                return new Promise(resolve => {
                    fetch(`set_tie.php?game_id=${gameId}`)
                        .then(response => response.json())
                        .then(() => {
                            winner = 0;
                            console.log('Tie!');
                            resolve(true);
                        });
                });
            }

            // get the winning player from the database and save it into a variable
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

            function isBoardFull() {
                return newGameState.every(cell => cell !== '-');
            }

            // check wether someone won
            function checkForWinner() {
                return new Promise(async (resolve) => {
                    let playerWon = false;

                    // rows
                    for (let i = 0; i <= 6; i += 3) {
                        if (newGameState[i] === playerSymbol &&
                            newGameState[i+1] === playerSymbol &&
                            newGameState[i+2] === playerSymbol) {
                            playerWon = true;
                        }
                    }
                    // columns
                    for (let i = 0; i <= 2; i++) {
                        if (newGameState[i] === playerSymbol &&
                            newGameState[i+3] === playerSymbol &&
                            newGameState[i+6] === playerSymbol) {
                            playerWon = true;
                        }
                    }
                    // diagonals
                    let dif = 4;
                    for (let i = 0; i <= 2; i += 2) {
                        if (newGameState[i] === playerSymbol &&
                            newGameState[i+dif] === playerSymbol &&
                            newGameState[i+2*dif] === playerSymbol) {
                            playerWon = true;
                        }
                        dif = 2;
                    }

                    if (playerWon) {
                        await setWinner();
                    } else if (isBoardFull()) {
                        await setTie();
                    }

                    resolve(true);
                });
            }

            // wits for the players turn
            // update game state, view, board and check for winner on turn change
            async function waitForTurn() {
                return new Promise(async(resolve) => {
                    // get player turn based on game id and save it to a variable
                    let response = await fetch('get_player_turn.php?game_id=<?php echo $game_id; ?>');
                    let data = await response.json();
                    currentTurn = data.current_turn;
                    
                    // update state and view of game
                    await updateGameState();
                    await updateBoardView();
                    
                    // check for winner and end game if there is one
                    console.log(`Winner: ${winner}`);
                    await getWinner();
                    if (winner !== -1) {
                        resolve('exit');
                        return;
                    }

                    // "my turn" update game state, view and board, else wait 2 seconds and poll again
                    if (currentTurn === playerNum) {
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

            // main game loop
            async function game() {
                // initialize board    
                enableBoard();

                // loop while game is active without a winner
                let exit = false;
                while (!exit) {
                    // wait for player to take turn
                    let result = await waitForTurn();
                    // check for winner and exit loop if there is one
                    if (result === 'exit') break;
                    console.log('wait')

                    // make move and check for winner, exit loop if there is one
                    await madeMove();
                    if (winner !== -1) break;

                    // swtich turns
                    console.log('move');
                    await changePlayerTurn();
                    
                    fetch('get_game_state.php?game_id=<?php echo $game_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.game_state)
                    })
                }

                // finalize game by showing winner and removing board
                finishGame();
            }

            // show the winner of the game
            function finishGame() {
                const overlay  = document.getElementById('result-modal-overlay');
                const icon     = document.getElementById('result-icon');
                const title    = document.getElementById('result-title');
                const subtitle = document.getElementById('result-subtitle');

                if (winner === 0) {
                    icon.textContent     = '🤝';
                    title.textContent    = "It's a Tie!";
                    subtitle.textContent = 'Nobody wins this round.';
                } else if (winner === playerNum) {
                    icon.textContent     = '🏆';
                    title.textContent    = 'You Won!';
                    subtitle.textContent = `Player ${winner} wins the game!`;
                } else {
                    icon.textContent     = '😔';
                    title.textContent    = `Player ${winner} Wins!`;
                    subtitle.textContent = 'Better luck next time.';
                }

                overlay.classList.add('show');
            }

            // set player symbol and turn  in database then start the game
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
        <div class="message" id="winner-message"></div>
        <div class="message" id="wait-message"></div>

        <div id="result-modal-overlay">
            <div id="result-modal">
                <div id="result-icon"></div>
                <h2 id="result-title"></h2>
                <p  id="result-subtitle"></p>
                <button id="result-close-btn" onclick="document.getElementById('result-modal-overlay').classList.remove('show')">
                Close
                </button>
                <button id="result-menu-btn" onclick="window.location.href='menu.php'">
                Main Menu
                </button>
            </div>
        </div>

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
            // if game status is 1 show waiting message and invite modal
            if (get_game_status($game_id) === 1) { ?>
                <script>
                   document.getElementById('wait-message').innerHTML = "<p>Waiting for other players</p>";
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
            // initialize game vairables
            let gameId = <?php echo $game_id; ?>;
            let userId = <?php echo $user_id; ?>;
            let playerNum = <?php echo $player_num; ?>;
            let isPlayerTurn = false;
            let playerSymbol = '';
            let currentTurn = -1;
            let winner = -1;
            // "null" game state
            let gameState = '---------';
            let newGameState = gameState.split('');

            console.log(gameState)

            let cells = document.querySelectorAll('.cell');

            // event listener for making a move
            cells.forEach(cell => {
                cell.addEventListener('click', function() {
                    if (!isPlayerTurn || winner != -1) return;
                    
                    console.log(cell.dataset.num)

                    // set cell to player symbol if its empty
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
                    // dont allow player to move on occupied cell
                });
            });
        </script>

        <script>
            gameActive();
        </script>
    </body>
</html>