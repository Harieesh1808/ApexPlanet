<?php
// includes/auth.php

// Start a secure session
if (session_status() === PHP_SESSION_NONE) {
    // Set secure cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '', 
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /task5/login.php");
        exit;
    }
}

function hasRole($role_name) {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === $role_name;
}

function requireRole($role_name) {
    requireLogin();
    if (!hasRole($role_name)) {
        header("HTTP/1.1 403 Forbidden");
        die("403 Forbidden: You do not have permission to access this page.");
    }
}

// CAPTCHA helper
function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha'] = $captcha;
    return $captcha;
}

// Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("CSRF Token Validation Failed");
    }
}
?>
