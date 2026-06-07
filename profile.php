<?php
session_start();

$user_id = $_SESSION['user_id'] ?? '';
if (empty($user_id)) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/profile.css">
    <title>Profile</title>
</head>
<body>

    <button id="back-btn" onclick="history.back()">← Back</button>

    <div id="profile-container">

        <div id="profile-header">
            <div id="profile-avatar">👤</div>
            <h1 id="profile-username">Loading...</h1>
            <p id="profile-id"></p>
        </div>

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

        <div id="match-history-section">
            <h2 id="match-history-title">Match History</h2>
            <div id="match-history-list">
                <p id="match-history-loading">Loading...</p>
            </div>
        </div>

    </div>

    <script src="js/profile.js"></script>
</body>
</html>
