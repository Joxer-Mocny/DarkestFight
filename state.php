<?php
// state.php – initializes or continues game session

// Start game state if not already present
if (!isset($_SESSION['hero_hp'])) {
    // Initialize hero stats and image
    $_SESSION['hero_hp'] = 50;
    $_SESSION['hero_state'] = 'HemaIdle.png';
}

if (!isset($_SESSION['monster_hp'])) {
    // Initialize monster stats and image
    $_SESSION['monster_hp'] = 50;
    $_SESSION['monster_state'] = 'PriseraIdle.png';
}

if (!isset($_SESSION['turn'])) {
    // Game always starts with player's turn
    $_SESSION['turn'] = 'player';
}

if (!isset($_SESSION['log'])) {
    // Initialize game log to track battle messages
    $_SESSION['log'] = [];
}

if (!isset($_SESSION['riposta_ready'])) {
    // Flag to track riposte preparation (used between turns)
    $_SESSION['riposta_ready'] = false;
}

if (!isset($_SESSION['bonus_damage'])) {
    // Flag to apply bonus damage on next attack if riposte was successful
    $_SESSION['bonus_damage'] = false;
}
