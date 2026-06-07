// -- Load profile stats -------------------------------------------------------
fetch('api/get_profile.php')
    .then(res => res.json())
    .then(data => {
        // Update profile info
        document.getElementById('profile-username').textContent      = data.username;
        document.getElementById('profile-id').textContent            = 'ID: ' + data.user_id;
        document.getElementById('stat-played-value').textContent     = data.games_played;
        document.getElementById('stat-won-value').textContent        = data.games_won;
        document.getElementById('stat-lost-value').textContent       = data.games_lost;
        document.getElementById('stat-winrate-value').textContent    = data.win_rate + '%';
    });

// -- Load match history -------------------------------------------------------
fetch('api/get_match_history.php')
    .then(res => res.json())
    .then(games => {
        const list = document.getElementById('match-history-list');
        list.innerHTML = '';

        // If no games, show empty message
        if (games.length === 0) {
            list.innerHTML = '<p id="match-history-empty">No games played yet.</p>';
            return;
        }

        // Sort games by most recent first
        games.forEach(game => {
            const row         = document.createElement('div');
            row.classList.add('match-row');
            row.id            = 'match-' + game.game_id;

            const resultClass = game.result === 'Win'  ? 'result-win'
                              : game.result === 'Loss' ? 'result-loss'
                              : 'result-tie';

            const winnerLabel = game.result === 'Tie' ? 'Tie' : '🏆 ' + game.winner_name;

            row.innerHTML = `
                <span class="match-result ${resultClass}">${game.result}</span>
                <span class="match-players">${game.player_one_name} vs ${game.player_two_name}</span>
                <span class="match-winner">${winnerLabel}</span>
            `;

            list.appendChild(row);
        });
    });
