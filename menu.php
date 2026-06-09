<?php
session_start();

$error_message = $_SESSION['code_error'] ?? "";
if (!empty($error_message)) unset($_SESSION['code_error']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="general_page_settings.css">
    <link rel="stylesheet" href="menu.css">
    <title>Menu</title>
    <script>
        function closeModal() {
            document.getElementById('modal-container').classList.remove('show-modal');
        }
        function openModal() {
            document.getElementById('modal-container').classList.add('show-modal');
        }
        function checkForErrors(event) {
            event.preventDefault();
            const codeError = document.getElementById('game-code-error');
            if (codeError.innerHTML === "") {
                document.getElementById("game_code_form").submit();
            }
        }
    </script>
</head>
<body>

<!-- Interactive animated canvas background -->
<canvas id="bg-canvas"></canvas>

<!-- Main menu card -->
<div id="menu-container">
    <h1>WELCOME TO<br>TIC-TAC-TOE</h1>

    <form action="create_game.php">
        <button type="submit">CREATE GAME</button>
    </form>

    <button id="join-btn" onclick="openModal()">JOIN GAME</button>

    <button id="profile-btn" onclick="window.location.href='profile.php'">PROFILE</button>
</div>

<!-- Join game modal -->
<div id="modal-container">
    <div id="join-modal">
        <button id="close-button" onclick="closeModal()">✕</button>
        <h2>JOIN GAME</h2>
        <p>Enter the 8-character game code:</p>

        <form id="game_code_form" method="post" action="join_game.php" autocomplete="off">
            <input type="text" id="game_code" name="game_code" maxlength="8" placeholder="XXXXXXXX" required>
            <div id="game-code-error">
                <?php if ($error_message): ?>
                    <script>document.addEventListener('DOMContentLoaded', openModal);</script>
                    <p>Invalid code!</p>
                <?php endif; ?>
            </div>
            <button type="button" id="join-submit-btn" onclick="checkForErrors(event)">JOIN</button>
        </form>
    </div>
</div>

<script>
    /* -----------------------------------------------------------------------
       Game-code input validation
    ----------------------------------------------------------------------- */
    document.getElementById('game_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        const code      = this.value;
        const codeError = document.getElementById('game-code-error');
        if (code.length < 8 && code.length > 0) {
            codeError.innerHTML = '<p>The code must be 8 characters long!</p>';
        } else {
            codeError.innerHTML = '';
        }
    });

    /* -----------------------------------------------------------------------
       Interactive tic-tac-toe canvas background
       Draws floating X and O tokens that drift slowly around the screen.
    ----------------------------------------------------------------------- */
    (function() {
        const canvas = document.getElementById('bg-canvas');
        const ctx    = canvas.getContext('2d');
        const TOKENS = [];
        const COUNT  = 18;

        function resize() {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        function randomToken() {
            const isX  = Math.random() > 0.5;
            const size = 30 + Math.random() * 40;
            return {
                x:    Math.random() * canvas.width,
                y:    Math.random() * canvas.height,
                vx:   (Math.random() - 0.5) * 0.5,
                vy:   (Math.random() - 0.5) * 0.5,
                size: size,
                isX:  isX,
                rot:  Math.random() * Math.PI * 2,
                vrot: (Math.random() - 0.5) * 0.01,
            };
        }

        for (let i = 0; i < COUNT; i++) TOKENS.push(randomToken());

        function drawX(x, y, size, rot) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rot);
            ctx.strokeStyle = 'white';
            ctx.lineWidth   = size * 0.15;
            ctx.lineCap     = 'round';
            const h = size * 0.4;
            ctx.beginPath();
            ctx.moveTo(-h, -h); ctx.lineTo(h, h);
            ctx.moveTo(h, -h);  ctx.lineTo(-h, h);
            ctx.stroke();
            ctx.restore();
        }

        function drawO(x, y, size, rot) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rot);
            ctx.strokeStyle = 'white';
            ctx.lineWidth   = size * 0.15;
            ctx.beginPath();
            ctx.arc(0, 0, size * 0.38, 0, Math.PI * 2);
            ctx.stroke();
            ctx.restore();
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (const t of TOKENS) {
                t.x   += t.vx;
                t.y   += t.vy;
                t.rot += t.vrot;

                // wrap around edges
                if (t.x < -60)                  t.x = canvas.width  + 60;
                if (t.x > canvas.width  + 60)   t.x = -60;
                if (t.y < -60)                  t.y = canvas.height + 60;
                if (t.y > canvas.height + 60)   t.y = -60;

                if (t.isX) drawX(t.x, t.y, t.size, t.rot);
                else       drawO(t.x, t.y, t.size, t.rot);
            }

            requestAnimationFrame(animate);
        }

        animate();
    })();
</script>

</body>
</html>