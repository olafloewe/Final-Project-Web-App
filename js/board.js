// -- Game variables (injected by board.php via data attributes) --------------
const boardEl    = document.getElementById('board');
let gameId       = parseInt(boardEl.dataset.gameId);
let userId       = parseInt(boardEl.dataset.userId);
let playerNum    = parseInt(boardEl.dataset.playerNum);

// -- Game state variables ------------------------------------------------------
let isPlayerTurn = false;
let playerSymbol = '';
let currentTurn  = -1;
let winner       = -1;
let gameState    = '---------';
let newGameState = gameState.split('');

const cells = document.querySelectorAll('.cell');

// game is over when winner is anything other than -1
function gameOver() { 
    return winner !== -1; 
}

// -- Board interaction -------------------------------------------------------
cells.forEach(cell => {
    cell.addEventListener('click', function () {
        if (!isPlayerTurn || gameOver()) return;
        if (cell.innerHTML !== '') return;

        // update the cell with the player's symbol
        if (playerSymbol === 'O') {
            cell.innerHTML = '<img src="./images/blue_circle.png" width="60" height="auto">';
        } else {
            cell.innerHTML = '<img src="./images/black_x.png" width="60" height="auto">';
        }

        newGameState[cell.dataset.num] = playerSymbol;
        console.log('New state:', newGameState.join(''));
    });
});

// -- API helpers --------------------------------------------------------------

// gets x or o for the current player
function getPlayerSymbol() {
    return fetch(`api/get_player_symbol.php?player_num=${playerNum}`)
        .then(res => res.json())
        .then(data => data.symbol);
}

// saves the current game state to the database
function setGameState() {
    return fetch(`api/set_game_state.php?game_id=${gameId}&game_state=${gameState}`)
        .then(() => console.log('Game state saved'));
}

// polls the database for the latest game state and updates local variables
function updateGameState() {
    return fetch(`api/get_game_state.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(data => {
            gameState = data.game_state;
            newGameState = gameState.split('');
        });
}

// polls the database for the current player's turn, updates local variable, and switches turn if needed
async function changePlayerTurn() {
    const res   = await fetch(`api/get_player_turn.php?game_id=${gameId}`);
    const data  = await res.json();
    currentTurn = data.current_turn;
    await fetch(`api/set_player_turn.php?game_id=${gameId}&current_turn=${currentTurn}`);
    console.log('Turn switched from', currentTurn);
}

// sets the winner in the database and updates local variable
function setWinner() {
    return fetch(`api/set_winner.php?game_id=${gameId}&winner=${userId}`)
        .then(res => res.json())
        .then(() => {
            winner = playerNum;
            console.log(`Winner set to playerNum: ${winner}`);
        });
}

// sets a tie in the database and updates local variable
function setTie() {
    return fetch(`api/set_tie.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(() => {
            winner = -2; // -2 = tie (0 and 1 are valid player numbers)
            console.log('Tie set');
        });
}

// polls the database for the winner and updates local variable
function getWinner() {
    return fetch(`api/get_winner.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(data => {
            winner = data.winner;
            console.log('Winner polled:', winner);
        });
}

// -- Board view ---------------------------------------------------------------
// adds the 'show' class to all cells to make them visible and enable interaction
function enableBoard() {
    cells.forEach(cell => cell.classList.add('show'));
}

// updates the board view based on the current game state, showing X and O images in the appropriate cells
function updateBoardView() {
    cells.forEach(cell => {
        const idx = cell.dataset.num; 
        if (gameState[idx] === 'X') {
            cell.innerHTML = '<img src="./images/black_x.png" width="60" height="auto">';
        } else if (gameState[idx] === 'O') {
            cell.innerHTML = '<img src="./images/blue_circle.png" width="60" height="auto">';
        }
    });
}

// checks if all cells are filled (no '-' characters in game state)
function isBoardFull() {
    return newGameState.every(cell => cell !== '-');
}

// checks for winning combinations (3 in a row, column, or diagonal) and calls setWinner or setTie as appropriate
async function checkForWinner() {
    let playerWon = false;

    // rows
    for (let i = 0; i <= 6; i += 3) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + 1] === playerSymbol &&
            newGameState[i + 2] === playerSymbol) playerWon = true;
    }
    // columns
    for (let i = 0; i <= 2; i++) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + 3] === playerSymbol &&
            newGameState[i + 6] === playerSymbol) playerWon = true;
    }
    // diagonals
    let step = 4;
    for (let i = 0; i <= 2; i += 2) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + step] === playerSymbol &&
            newGameState[i + 2 * step] === playerSymbol) playerWon = true;
        step = 2;
    }

    // if player won, set winner; if board is full and no winner, set tie
    if (playerWon) {
        await setWinner();
    } else if (isBoardFull()) {
        await setTie();
    }
}

// -- Game loop ----------------------------------------------------------------
// waits for the player to make a move by polling the game state until it changes, then checks for winner and updates turn
async function waitForMove() {
    while (gameState === newGameState.join('')) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    isPlayerTurn = false;
    gameState    = newGameState.join('');
    await setGameState();
    await checkForWinner();
}

// waits for the player's turn by polling the current turn from the database, otherwise continues polling
async function waitForTurn() {
    const res = await fetch(`api/get_player_turn.php?game_id=${gameId}`);
    const data = await res.json();
    currentTurn = data.current_turn;

    await updateGameState();
    updateBoardView();
    await getWinner();

    if (gameOver()) return 'exit';

    if (currentTurn === playerNum) {
        console.log('Your turn');
        isPlayerTurn = true;
        return 'play';
    }

    console.log('Not your turn, polling...');
    await new Promise(resolve => setTimeout(resolve, 2000));
    return waitForTurn();
}

// main game loop
async function runGame() {
    enableBoard();

    while (true) {
        const turnResult = await waitForTurn();
        if (turnResult === 'exit') break;

        await waitForMove();
        if (gameOver()) break;

        await changePlayerTurn();
    }

    showResultModal();
}

// -- Result modal -------------------------------------------------------------
// modal content based on the winner and shows the modal
function showResultModal() {
    const overlay  = document.getElementById('result-modal-overlay');
    const icon     = document.getElementById('result-icon');
    const title    = document.getElementById('result-title');
    const subtitle = document.getElementById('result-subtitle');

    // winner is 0 or 1 (internal player num), display as "Player 1" or "Player 2"
    const winnerLabel = `Player ${winner + 1}`;
    const otherLabel  = `Player ${(playerNum === 0) ? 2 : 1}`;

    // set modal content based on winner (-2 = tie, playerNum = win, other player = loss)
    if (winner === -2) {
        icon.textContent     = '🤝';
        title.textContent    = "It's a Tie!";
        subtitle.textContent = 'Nobody wins this round.';
    } else if (winner === playerNum) {
        icon.textContent     = '🏆';
        title.textContent    = 'You Won!';
        subtitle.textContent = `${otherLabel} lost this round.`;
    } else {
        icon.textContent     = '😔';
        title.textContent    = `${winnerLabel} Wins!`;
        subtitle.textContent = 'Better luck next time.';
    }
 
    overlay.classList.add('show');
}

// hides the result modal
function closeResultModal() {
    document.getElementById('result-modal-overlay').classList.remove('show');
}

// -- Invite modal -------------------------------------------------------------
// copies the game code to the clipboard and shows an alert
function copyGameCode() {
    const code = document.getElementById('game-code').textContent;
    navigator.clipboard.writeText(code).then(() => alert('Code copied to clipboard!'));
}

// closes the invite modal
function closeInviteModal() {
    document.getElementById('invite-modal-overlay').classList.remove('show');
}

// -- Game status polling ------------------------------------------------------
// polls the database for the game status, and starts the game when both players have joined
function pollGameStatus() {
    fetch(`api/check_game_status.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(data => {
            if (data.game_status === 2) {
                document.getElementById('wait-message').innerHTML = '';
                startGame();
            } else {
                setTimeout(pollGameStatus, 2000);
            }
        });
}

// starts the game by getting the player's symbol and setting the initial turn if this player is player 1
function startGame() {
    getPlayerSymbol().then(symbol => {
        playerSymbol = symbol;
        if (playerNum === 0) {
            fetch(`api/set_player_turn.php?game_id=${gameId}&current_turn=1`)
                .then(() => runGame());
        } else {
            runGame();
        }
    });
}

// -- Boot --------------------------------------------------------------------
pollGameStatus();