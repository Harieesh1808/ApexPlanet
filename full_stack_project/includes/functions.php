<?php
// includes/functions.php

/**
 * Sanitize output for HTML to prevent XSS
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a CSRF token and store it in session
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST request
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("CSRF token validation failed.");
    }
}

/**
 * Check if the user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the logged-in user is an admin
 */
function is_admin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1; // Assuming role_id 1 is admin
}

/**
 * Redirect and exit
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Require the user to be logged in, otherwise redirect to login
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = "You must be logged in to view that page.";
        redirect('/task4/login.php');
    }
}

/**
 * Require the user to be an admin, otherwise redirect to home
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        $_SESSION['flash_error'] = "Access denied. Administrators only.";
        redirect('/task4/index.php');
    }
}
?>
