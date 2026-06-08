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
                document.getElementById('modal_container').classList.remove('show-modal');
            }

            function openModal() {
                document.getElementById('modal_container').classList.add('show-modal');
            }

            function checkForErrors(event) {
                event.preventDefault(); 
                let codeErrorDiv = document.getElementById('game_code_error');

                if (codeErrorDiv.innerHTML === "") {
                    document.getElementById("game_code_form").submit();
                }
            }
        </script>
    </head>

    <body>
                <div class="bg-wrapper">
                    <div class="trigger-zone zone-1">
                        <div class="float-token x t1"></div>
                        <div class="float-token o t2"></div>
                        <div class="float-token x t3"></div>
                        <div class="float-token o t4"></div>
                    </div>
                    <div class="trigger-zone zone-2">
                        <div class="float-token o t1"></div>
                        <div class="float-token x t2"></div>
                        <div class="float-token o t3"></div>
                        <div class="float-token x t4"></div>
                    </div>
                    <div class="trigger-zone zone-3">
                        <div class="float-token o t1"></div>
                        <div class="float-token x t2"></div>
                        <div class="float-token x t3"></div>
                        <div class="float-token o t4"></div>
                    </div>
                    <div class="trigger-zone zone-4">
                        <div class="float-token x t1"></div>
                        <div class="float-token o t2"></div>
                        <div class="float-token o t3"></div>
                        <div class="float-token x t4"></div>
                    </div>
                </div>
        <div id="menu_container">
            <h1>WELCOME TO TIC-TAC-TOE</h1>
            <form action="create_game.php">
                <button>CREATE GAME</button>
            </form>
            
            <button onClick="openModal()">JOIN GAME</button>
            
            <form action="">
                <button>JOIN RANDOM GAME</button>
            </form>
        </div>

        <div id="modal_container" class="modal_container">
            <div id="join_modal">
                <h2>JOIN GAME!</h2>
                <p>Please input the code below:</p>
                
                <form id="game_code_form" method="post" action="join_game.php" autocomplete="off">
                    <input type="text" id="game_code" name="game_code" maxlength="8" required>
                    <div class="error" id="game_code_error">
                        <?php
                            if ($error_message) {
                                echo '<script>openModal()</script>';
                                /* CHANGED SECTION: Removed inline style="color: red;" 
                                   so it pulls from the cleaner CSS alert box styling */
                                echo '<p>Invalid code!</p>';
                            }
                        ?>
                    </div>
                    <button onClick="checkForErrors(event)">JOIN</button>
                </form>
                <button id="close-button" onclick="closeModal()">X</button>
            </div>
        </div>

        <script>
            document.getElementById('game_code').addEventListener('input', function() {
                this.value = this.value.toUpperCase();

                let code = this.value;
                let codeErrorDiv = document.getElementById('game_code_error');
                if (code.length < 8 && code.length > 0) {
                    /* CHANGED SECTION: Removed inline style="color: red;" 
                       to keep presentation rules decoupled and localized inside menu.css */
                    codeErrorDiv.innerHTML = '<p>The code must be 8 characters long!</p>';
                } else {
                    codeErrorDiv.innerHTML = '';
                }
            });
        </script>
    </body>
</html>