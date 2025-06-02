<?php
// index.php – Main UI for the AJAX-powered PHP game with delayed monster turn
session_start();
require_once 'state.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Darkest Fight - PHP Game</title>
  <link rel="stylesheet" href="style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1>Darkest Fight</h1>

<!-- Game Log -->
<div class="log" id="log">
  <?php foreach (array_slice($_SESSION['log'], -4) as $entry): ?>
    <?= htmlspecialchars($entry) ?><br>
  <?php endforeach; ?>
</div>

<!-- Monster is preparing text -->
<div id="monster-prepare" class="monster-prepare"></div>

<!-- Battlefield with hero and monster -->
<div class="battlefield no-wrap">
  <div class="hero-container">
    <div class="hp-label" id="hero-hp">Hero HP: <?= $_SESSION['hero_hp'] ?></div>
    <img id="hero-img" src="assets/<?= $_SESSION['hero_state'] ?>" alt="Hero" class="hero-img">
  </div>
  <div class="monster-container">
    <div class="hp-label" id="monster-hp">Monster HP: <?= $_SESSION['monster_hp'] ?></div>
    <img id="monster-img" src="assets/<?= $_SESSION['monster_state'] ?>" alt="Monster" class="monster-img">
  </div>
</div>

<!-- Action Buttons -->
<div class="controls" id="controls">
  <button onclick="sendAction('attack')">Attack</button>
  <button onclick="sendAction('riposta')">Riposte</button>
  <button onclick="resetGame()">🔁 Restart</button>
</div>

<script>
function sendAction(action) {
  fetch('game.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=' + action
  })
  .then(res => res.json())
  .then(data => {
    updateGameUI(data);

    if (data.awaiting_monster) {
      // Show monster preparing text
      const prepDiv = document.getElementById('monster-prepare');
      prepDiv.textContent = 'MONSTER IS PREPARING TO ATTACK...';
      prepDiv.className = 'monster-prepare-active';

      setTimeout(() => {
        fetch('game.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=monster_turn'
        })
        .then(res => res.json())
        .then(data => {
          updateGameUI(data);
          prepDiv.textContent = '';
          prepDiv.className = '';
        });
      }, 3000);
    }
  });
}

function resetGame() {
  fetch('reset.php', { method: 'POST' }).then(() => location.reload());
}

function updateGameUI(data) {
  const heroImg = document.getElementById('hero-img');
  const monsterImg = document.getElementById('monster-img');

  document.getElementById('hero-hp').textContent = '🧕 Hero HP: ' + data.hero_hp;
  document.getElementById('monster-hp').textContent = '👹 Monster HP: ' + data.monster_hp;

  // Apply hit class for shake effect
  if (data.hero_state.includes('Zasah')) {
    heroImg.classList.add('hit');
  } else {
    heroImg.classList.remove('hit');
  }
  if (data.monster_state.includes('Zasah')) {
    monsterImg.classList.add('hit');
  } else {
    monsterImg.classList.remove('hit');
  }

  heroImg.src = 'assets/' + data.hero_state;
  monsterImg.src = 'assets/' + data.monster_state;

  const logDiv = document.getElementById('log');
  logDiv.innerHTML = data.log.slice(-4).join('<br>');

  if (data.hero_hp <= 0 || data.monster_hp <= 0) {
    const message = data.hero_hp <= 0
      ? '<div class=\"final-text you-died\">YOU DIED</div>'
      : '<div class=\"final-text monster-slain\">MONSTER IS SLAIN</div>';

    document.getElementById('controls').innerHTML =
      message + '<br><button onclick=\"resetGame()\">🔁 Restart</button>';
  }
}
</script>
</body>
</html>
