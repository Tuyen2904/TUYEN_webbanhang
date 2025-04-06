<?php
class SessionHelper {
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['username']);
    }

    public static function isAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['username']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
?>
