<?php
/**
 * Task-3 Deliverable: Session Logout Handler
 * File: logout.php
 * Clears session data, destroys active session, and redirects to auth.html.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: auth.html?action=login&msg=logged_out");
exit;
