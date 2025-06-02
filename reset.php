<?php
// reset.php – Securely clears session and restarts the game

session_start();

// Clear all session data
$_SESSION = [];

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Return OK for AJAX refresh
http_response_code(200);
