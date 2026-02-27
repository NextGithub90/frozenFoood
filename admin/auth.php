<?php
// ========================================
// AUTH: Session & Authentication Helper
// ========================================

require_once __DIR__ . '/../config.php';

// Admin credentials (hardcoded)
define('ADMIN_USERNAME', 'frozen');
define('ADMIN_PASSWORD', 'frozen123');

function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        redirect(BASE_URL . 'admin/login.php');
    }
}

function attemptLogin($username, $password)
{
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }
    return false;
}

function logout()
{
    session_destroy();
    redirect(BASE_URL . 'admin/login.php');
}
