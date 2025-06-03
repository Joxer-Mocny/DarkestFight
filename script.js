// Sends the player's action (attack or riposte) to the server
function sendAction(action) {
  // Select the attack and riposte buttons
  const attackBtn = document.querySelector('button[onclick*="attack"]');
  const riposteBtn = document.querySelector('button[onclick*="riposta"]');

  // Disable the buttons for 3 seconds to prevent spamming
  attackBtn.disabled = true;
  riposteBtn.disabled = true;
  setTimeout(() => {
    attackBtn.disabled = false;
    riposteBtn.disabled = false;
  }, 3000);

  // Send the player's action to game.php via POST
  fetch('game.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=' + action
  })
  .then(res => res.json()) // Parse the JSON response
  .then(data => {
    updateGameUI(data); // Update the UI based on the new state

    // If the monster will attack after a delay
    if (data.awaiting_monster) {
      const prepDiv = document.getElementById('monster-prepare');
      prepDiv.textContent = 'Monster is preparing to attack...';
      prepDiv.classList.add('monster-prepare-active');

      // Wait 3 seconds, then trigger the monster's attack
      setTimeout(() => {
        fetch('game.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=monster_turn'
        })
        .then(res => res.json())
        .then(data => {
          updateGameUI(data); // Update UI again after monster attacks
          prepDiv.textContent = '';
          prepDiv.classList.remove('monster-prepare-active');
        });
      }, 3000);
    }
  });
}

// Resets the game by sending a request to reset.php and reloading the page
function resetGame() {
  fetch('reset.php', { method: 'POST' }).then(() => location.reload());
}

// Updates all UI elements based on the game state from the server
function updateGameUI(data) {
  const heroImg = document.getElementById('hero-img');
  const monsterImg = document.getElementById('monster-img');

  // Update health values for both characters
  document.getElementById('hero-hp').textContent = ' Hero HP: ' + data.hero_hp;
  document.getElementById('monster-hp').textContent = ' Monster HP: ' + data.monster_hp;

  // Add shake animation if hit
  heroImg.classList.toggle('hit', data.hero_state.includes('Zasah'));
  monsterImg.classList.toggle('hit', data.monster_state.includes('Zasah'));

  // Update the image sources to reflect the current state (e.g., attack, idle, hit)
  heroImg.src = 'assets/' + data.hero_state;
  monsterImg.src = 'assets/' + data.monster_state;

  // Display the last 4 log messages
  const logDiv = document.getElementById('log');
  logDiv.innerHTML = data.log.slice(-4).join('<br>');

  // Show or hide monster preparation message
  const prepDiv = document.getElementById('monster-prepare');
  if (data.awaiting_monster) {
    prepDiv.textContent = 'Monster is preparing to attack...';
    prepDiv.classList.add('monster-prepare-active'); 
  } else {
    prepDiv.textContent = '';
    prepDiv.classList.remove('monster-prepare-active');
  }

  // If the game is over, show the final result and restart button
  if (data.hero_hp <= 0 || data.monster_hp <= 0) {
    const message = data.hero_hp <= 0
      ? '<div class="final-text you-died">YOU DIED</div>'
      : '<div class="final-text monster-slain">MONSTER IS SLAIN</div>';

    document.getElementById('controls').innerHTML =
      message + '<br><button onclick="resetGame()">Restart</button>';
  }
}
