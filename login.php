<?php
session_start();

// check for error message
$error_message = $_SESSION['login_error'] ?? "";

// unset error message if it exists
if (!empty($error_message)) unset($_SESSION['login_error']);

// check for successful sign up message
$sign_up_message = $_SESSION['sign_up_message'] ?? "";

// unset successful sign up message if it exists
if (!empty($sign_up_message)) unset($_SESSION['sign_up_message']);
?>

<html>
    <head>
        <link rel="stylesheet" href="general_page_settings.css">
        <link rel="stylesheet" href="login.css">
        <title>Login</title>    
    </head>
      
    <body>
        <div class="container" id="login_form">
            <h1>LOGIN</h1>
            
            <?php
                // display successful sign up message if it exists
                if ($sign_up_message) {
                    echo '<p style="color: white;">The sign up was successful!<br>Please login below</p>';
                }
            ?>

            
            <form method="post" action="login_check_credentials.php" autocomplete="off">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <?php
                    // display error message if it exists
                    if ($error_message) {
                        echo '<p style="color: red;">Incorrect username or password!</p>';
                    }
                ?>
                
                <button type="submit">LOGIN</button>
            </form>

            <div id="sign_up">
                <p>Don't have an account?</p>
                <a href="sign_up.html">Sign up!</a>
            </div>
        </div>
    </body>
</html>