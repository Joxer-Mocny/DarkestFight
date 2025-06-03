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

<!-- Monster attack notice -->
<div id="monster-prepare" class="monster-prepare"></div>

<!-- Game Log -->
<div class="log" id="log">
  <?php foreach (array_slice($_SESSION['log'], -4) as $entry): ?>
    <?= htmlspecialchars($entry) ?><br>
  <?php endforeach; ?>
</div>

<!-- Battlefield -->
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

<!-- Controls -->
<div class="controls" id="controls">
  <button onclick="sendAction('attack')">Attack</button>
  <button onclick="sendAction('riposta')">Riposte</button>
  <button onclick="resetGame()">Restart</button>
</div>

<script src="script.js"></script>
</body>
</html>
