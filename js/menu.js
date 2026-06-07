// -- Join game modal ----------------------------------------------------------
function openJoinModal() {
    document.getElementById('join-modal-overlay').classList.add('show');
}

function closeJoinModal() {
    document.getElementById('join-modal-overlay').classList.remove('show');
}

// -- Game code input validation -----------------------------------------------
function validateGameCode(event) {
    event.preventDefault();
    const errorDiv = document.getElementById('game-code-error');
    if (errorDiv.innerHTML === '') {
        document.getElementById('join-game-form').submit();
    }
}

// -- Create game modal --------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const codeInput = document.getElementById('game-code-input');
    if (!codeInput) return;

    codeInput.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
        const errorDiv = document.getElementById('game-code-error');
        if (this.value.length > 0 && this.value.length < 8) {
            errorDiv.innerHTML = '<p class="error-text">The code must be 8 characters long!</p>';
        } else {
            errorDiv.innerHTML = '';
        }
    });
});
