<?php
// game.php – handles all game logic per turn
session_start();

// Initialize state if missing (failsafe)
if (!isset($_SESSION['hero_hp']) || !isset($_SESSION['monster_hp'])) {
    header('Location: reset.php'); // redirect if session expired or missing
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'attack':
        // Player chooses to attack
        $heroAttack = rand(1, 10); // random damage roll
        $monsterDefense = rand(1, 10); // monster rolls defense

        if ($heroAttack > $monsterDefense) {
            $damage = $heroAttack - $monsterDefense;

            // Check for riposte bonus
            if (!empty($_SESSION['bonus_damage'])) {
                $damage *= 3;
                unset($_SESSION['bonus_damage']);
                $_SESSION['log'][] = "Riposte! Bonus damage x3 applied.";
            }

            $_SESSION['monster_hp'] -= $damage;
            $_SESSION['monster_state'] = 'PriseraZasah.png'; // Monster hit animation
            $_SESSION['hero_state'] = 'HemaUtok.png';         // Hero attack animation
            $_SESSION['log'][] = "Hero hits Monster for $damage";
        } else {
            // Monster successfully dodged
            $_SESSION['monster_state'] = 'PriseraIdle.png'; // Default idle (no explicit dodge image)
            $_SESSION['hero_state'] = 'HemaUtok.png';
            $_SESSION['log'][] = "Hero's attack missed";
        }
        $_SESSION['turn'] = 'monster'; // next turn is monster
        break;

    case 'riposta':
        // Player activates Riposte stance
        $_SESSION['riposta_ready'] = true;
        $_SESSION['hero_state'] = 'HemaIdle.png'; // No dedicated dodge image, fallback to idle
        $_SESSION['log'][] = "Hero is preparing a Riposte";
        $_SESSION['turn'] = 'monster';
        break;

    case 'do_monster_attack':
        // Monster auto-attacks after timeout
        $monsterAttack = rand(1, 10);
        $heroDefense = rand(1, 10);

        if ($monsterAttack > $heroDefense) {
            $damage = $monsterAttack - $heroDefense;
            $_SESSION['hero_hp'] -= $damage;
            $_SESSION['monster_state'] = 'PriseraUtok.png'; // Monster attack animation
            $_SESSION['hero_state'] = 'HemaZasah.png';       // Hero hit animation
            $_SESSION['log'][] = "Monster hits Hero for $damage";
        } else {
            // Hero dodged monster's attack
            $_SESSION['monster_state'] = 'PriseraUtok.png'; // Still shows attack attempt
            $_SESSION['hero_state'] = 'HemaIdle.png';       // Fallback to idle on miss
            $_SESSION['log'][] = "Monster missed";
        }

        // If Riposte was previously triggered, activate bonus on next player attack
        if ($_SESSION['riposta_ready'] ?? false) {
            $_SESSION['riposta_ready'] = false;
            $_SESSION['bonus_damage'] = true;
        }

        $_SESSION['turn'] = 'player'; // player's turn resumes
        break;
}

// Redirect back to main view
header('Location: index.php');
exit();
