<?php
// game.php – Processes player actions and handles monster turn separately

session_start();
require_once 'state.php';

$action = $_POST['action'] ?? '';
$response = [];

if ($action === 'attack' || $action === 'riposta') {
  // Player's turn
  if ($action === 'attack') {
    $heroAttack = rand(1, 10);
    $monsterDefense = rand(1, 10);

    if ($heroAttack > $monsterDefense) {
      $damage = $heroAttack - $monsterDefense;

      if (!empty($_SESSION['bonus_damage'])) {
        $damage *= 3;
        $_SESSION['log'][] = "Riposte! Bonus damage x3 applied.";
        unset($_SESSION['bonus_damage']);
      }

      $_SESSION['monster_hp'] -= $damage;
      $_SESSION['monster_state'] = 'PriseraZasah.png';
      $_SESSION['hero_state'] = 'HemaUtok.png';
      $_SESSION['log'][] = "Hero hits Monster for $damage";
    } else {
      $_SESSION['monster_state'] = 'PriseraIdle.png';
      $_SESSION['hero_state'] = 'HemaUtok.png';
      $_SESSION['log'][] = "Hero's attack missed";
    }
  } elseif ($action === 'riposta') {
    $_SESSION['riposta_ready'] = true;
    $_SESSION['hero_state'] = 'HemaIdle.png';
    $_SESSION['log'][] = "Hero is preparing a Riposte";
  }

  $_SESSION['awaiting_monster'] = true;
} elseif ($action === 'monster_turn') {
  // Monster's delayed turn (triggered by frontend after 3s)
  $monsterAttack = rand(1, 10);
  $heroDefense = rand(1, 10);

  if ($monsterAttack > $heroDefense) {
    $damage = $monsterAttack - $heroDefense;
    $_SESSION['hero_hp'] -= $damage;
    $_SESSION['monster_state'] = 'PriseraUtok.png';
    $_SESSION['hero_state'] = 'HemaZasah.png';
    $_SESSION['log'][] = "Monster hits Hero for $damage";
  } else {
    $_SESSION['monster_state'] = 'PriseraUtok.png';
    $_SESSION['hero_state'] = 'HemaIdle.png';
    $_SESSION['log'][] = "Monster missed";
  }

  if ($_SESSION['riposta_ready'] ?? false) {
    $_SESSION['riposta_ready'] = false;
    $_SESSION['bonus_damage'] = true;
  }

  $_SESSION['awaiting_monster'] = false;
}

// Return the updated game state
header('Content-Type: application/json');
echo json_encode([
  'hero_hp' => $_SESSION['hero_hp'],
  'monster_hp' => $_SESSION['monster_hp'],
  'hero_state' => $_SESSION['hero_state'],
  'monster_state' => $_SESSION['monster_state'],
  'log' => array_slice($_SESSION['log'], -10),
  'awaiting_monster' => $_SESSION['awaiting_monster']
]);
