<?php
session_start();

$user_id = $_SESSION['user_id'] ?? '';

// if not logged in, redirect to login page
if (empty($user_id)) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="general_page_settings.css">
    <link rel="stylesheet" href="profile.css">
    <title>Profile</title>
</head>
<body>

    <button id="profile-back-btn" onclick="history.back()">← Back</button>
    <!-- Profile Header -->
    <div id="profile-container">
        <div id="profile-header">
            <div id="profile-avatar">👤</div>
            <h1 id="profile-username">Loading...</h1>
            <p id="profile-id"></p>
        </div>

        <!-- Profile Statistics -->
        <div id="profile-stats">
            <div class="stat-card" id="stat-played">
                <div class="stat-value" id="stat-played-value">—</div>
                <div class="stat-label">Games Played</div>
            </div>
            <div class="stat-card" id="stat-won">
                <div class="stat-value" id="stat-won-value">—</div>
                <div class="stat-label">Games Won</div>
            </div>
            <div class="stat-card" id="stat-lost">
                <div class="stat-value" id="stat-lost-value">—</div>
                <div class="stat-label">Games Lost</div>
            </div>
            <div class="stat-card" id="stat-winrate">
                <div class="stat-value" id="stat-winrate-value">—</div>
                <div class="stat-label">Win Rate</div>
            </div>
        </div>
    </div>

    <!-- Profile Scripts -->
    <script>
        fetch('get_profile.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('profile-username').textContent  = data.username;
                document.getElementById('profile-id').textContent        = 'ID: ' + data.user_id;
                document.getElementById('stat-played-value').textContent = data.games_played;
                document.getElementById('stat-won-value').textContent    = data.games_won;
                document.getElementById('stat-lost-value').textContent   = data.games_lost;
                document.getElementById('stat-winrate-value').textContent = data.win_rate + '%';
            });
    </script>

</body>
</html>