// -- Game variables (injected by board.php via data attributes) --------------
const boardEl    = document.getElementById('board');
let gameId       = parseInt(boardEl.dataset.gameId);
let userId       = parseInt(boardEl.dataset.userId);
let playerNum    = parseInt(boardEl.dataset.playerNum);

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

function setGameState() {
    return fetch(`api/set_game_state.php?game_id=${gameId}&game_state=${gameState}`)
        .then(() => console.log('Game state saved'));
}

function updateGameState() {
    return fetch(`api/get_game_state.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(data => {
            gameState = data.game_state;
            newGameState = gameState.split('');
        });
}

async function changePlayerTurn() {
    const res = await fetch(`api/get_player_turn.php?game_id=${gameId}`);
    const data = await res.json();
    currentTurn = data.current_turn;
    await fetch(`api/set_player_turn.php?game_id=${gameId}&current_turn=${currentTurn}`);
    console.log('Turn switched from', currentTurn);
}

function setWinner() {
    return fetch(`api/set_winner.php?game_id=${gameId}&winner=${userId}`)
        .then(res => res.json())
        .then(data => {
            winner = data.winner;
            console.log('Winner set:', winner);
        });
}

function setTie() {
    return fetch(`api/set_tie.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(() => {
            winner = -2; // tie
            console.log('Tie set');
        });
}

function getWinner() {
    return fetch(`api/get_winner.php?game_id=${gameId}`)
        .then(res => res.json())
        .then(data => {
            winner = data.winner;
            console.log('Winner polled:', winner);
        });
}

// -- Board view ---------------------------------------------------------------
function enableBoard() {
    cells.forEach(cell => cell.classList.add('show'));
}

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

// -- Win / tie detection ------------------------------------------------------
function isBoardFull() {
    return newGameState.every(cell => cell !== '-');
}

async function checkForWinner() {
    let playerWon = false;

    // rows
    for (let i = 0; i <= 6; i += 3) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + 1] === playerSymbol &&
            newGameState[i + 2] === playerSymbol) {
            playerWon = true;
        }
    }
    // columns
    for (let i = 0; i <= 2; i++) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + 3] === playerSymbol &&
            newGameState[i + 6] === playerSymbol) {
            playerWon = true;
        }
    }
    // diagonals
    let step = 4;
    for (let i = 0; i <= 2; i += 2) {
        if (newGameState[i] === playerSymbol &&
            newGameState[i + step] === playerSymbol &&
            newGameState[i + 2 * step] === playerSymbol) {
            playerWon = true;
        }
        step = 2;
    }

    if (playerWon) {
        await setWinner();
    } else if (isBoardFull()) {
        await setTie();
    }
}

// -- Game loop ----------------------------------------------------------------
async function waitForMove() {
    while (gameState === newGameState.join('')) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    isPlayerTurn = false;
    gameState    = newGameState.join('');
    await setGameState();
    await checkForWinner();
}

async function waitForTurn() {
    const res = await fetch(`api/get_player_turn.php?game_id=${gameId}`);
    const data = await res.json();
    currentTurn = data.current_turn;

    await updateGameState();
    updateBoardView();
    await getWinner();

    if (winner !== -1) return 'exit';

    if (currentTurn === playerNum) {
        console.log('Your turn');
        isPlayerTurn = true;
        return 'play';
    }

    console.log('Not your turn, polling...');
    await new Promise(resolve => setTimeout(resolve, 2000));
    return waitForTurn();
}

async function runGame() {
    enableBoard();

    while (true) {
        const turnResult = await waitForTurn();
        if (turnResult === 'exit') break;

        await waitForMove();
        if (winner !== -1) break;

        await changePlayerTurn();
    }

    showResultModal();
}

// -- Result modal -------------------------------------------------------------
function showResultModal() {
    const overlay  = document.getElementById('result-modal-overlay');
    const icon     = document.getElementById('result-icon');
    const title    = document.getElementById('result-title');
    const subtitle = document.getElementById('result-subtitle');
 
    if (winner === -2) {
        // tie
        icon.textContent     = '🤝';
        title.textContent    = "It's a Tie!";
        subtitle.textContent = 'Nobody wins this round.';
    } else if (winner === playerNum) {
        // this player won
        icon.textContent     = '🏆';
        title.textContent    = 'You Won!';
        subtitle.textContent = `Player ${winner + 1} wins the game!`;
    } else {
        // other player won
        icon.textContent     = '😔';
        title.textContent    = `Player ${winner + 1} Wins!`;
        subtitle.textContent = 'Better luck next time.';
    }
 
    overlay.classList.add('show');
}

function closeResultModal() {
    document.getElementById('result-modal-overlay').classList.remove('show');
}

// -- Invite modal -------------------------------------------------------------
function copyGameCode() {
    const code = document.getElementById('game-code').textContent;
    navigator.clipboard.writeText(code).then(() => alert('Code copied to clipboard!'));
}

function closeInviteModal() {
    document.getElementById('invite-modal-overlay').classList.remove('show');
}

// -- Game status polling ------------------------------------------------------
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

// -- Boot ---------------------------------------------------------------------
pollGameStatus();