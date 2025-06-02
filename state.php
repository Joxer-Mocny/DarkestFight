<?php
// state.php – initializes game session state if not already set

if (!isset($_SESSION['hero_hp'])) {
  $_SESSION['hero_hp'] = 30;
  $_SESSION['hero_state'] = 'HemaIdle.png';
}

if (!isset($_SESSION['monster_hp'])) {
  $_SESSION['monster_hp'] = 30;
  $_SESSION['monster_state'] = 'PriseraIdle.png';
}

if (!isset($_SESSION['log'])) {
  $_SESSION['log'] = [];
}

if (!isset($_SESSION['riposta_ready'])) {
  $_SESSION['riposta_ready'] = false;
}

if (!isset($_SESSION['bonus_damage'])) {
  $_SESSION['bonus_damage'] = false;
}
