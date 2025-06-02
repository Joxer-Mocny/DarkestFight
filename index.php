<?php
// index.php – Main UI and rendering logic
session_start();
require_once 'state.php'; // Initialize game state (HPs, images, logs)
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

<!-- Display the last 4 battle log entries -->
<div class="log">
    <?php foreach (array_slice($_SESSION['log'], -4) as $entry): ?>
        <?= htmlspecialchars($entry) ?><br>
    <?php endforeach; ?>
</div>

<!-- Battlefield: hero and monster images with HP -->
<div class="battlefield no-wrap">
    <div class="hero-container">
        <div class="hp-label">🧕 Hero HP: <?= $_SESSION['hero_hp'] ?></div>
        <?php
        $heroClass = ($_SESSION['hero_state'] === 'HemaZasah.png') ? 'hero-img hit' : 'hero-img';
        ?>
        <img src="assets/<?= $_SESSION['hero_state'] ?>" alt="Hero" class="<?= $heroClass ?>">
    </div>
    <div class="monster-container">
        <div class="hp-label">👹 Monster HP: <?= $_SESSION['monster_hp'] ?></div>
        <?php
        $monsterClass = ($_SESSION['monster_state'] === 'PriseraZasah.png') ? 'monster-img hit' : 'monster-img';
        ?>
        <img src="assets/<?= $_SESSION['monster_state'] ?>" alt="Monster" class="<?= $monsterClass ?>">
    </div>
</div>

<!-- Action buttons depending on the current turn -->
<div class="controls">
<?php if ($_SESSION['hero_hp'] <= 0): ?>
    <h2 class="defeat">💀 Hero has been defeated!</h2>
<?php elseif ($_SESSION['monster_hp'] <= 0): ?>
    <h2 class="victory">🏆 Monster has been slain!</h2>
<?php elseif ($_SESSION['turn'] === 'player'): ?>
    <!-- Player's turn: show action buttons -->
    <form method="POST" action="game.php">
        <button type="submit" name="action" value="attack">Attack</button>
        <button type="submit" name="action" value="riposta">Riposte</button>
    </form>
<?php elseif ($_SESSION['turn'] === 'monster'): ?>
    <!-- Monster will auto-attack after 3 seconds -->
    <h2 class="countdown">👹 Monster attacks in 3 seconds...</h2>
    <script>
        setTimeout(function() {
            fetch('game.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=do_monster_attack'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }, 3000);
    </script>
<?php endif; ?>
</div>

<!-- Restart button when battle is over -->
<?php if ($_SESSION['hero_hp'] <= 0 || $_SESSION['monster_hp'] <= 0): ?>
    <form method="POST" action="reset.php">
        <button type="submit">🔁 Restart Game</button>
    </form>
<?php endif; ?>

</body>
</html>