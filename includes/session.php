<?php
// Secure session configuration
if (session_status() == PHP_SESSION_NONE) {
    // Configure session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Set session name
    session_name('PETROMINE_SESSION');
    
    // Start session
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Check for session timeout (30 minutes of inactivity)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
    
    // Validate session fingerprint to prevent session hijacking
    $fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    if (isset($_SESSION['fingerprint'])) {
        if ($_SESSION['fingerprint'] !== $fingerprint) {
            session_unset();
            session_destroy();
            session_start();
        }
    } else {
        $_SESSION['fingerprint'] = $fingerprint;
    }
}
?>