<?php
// Authentication helper functions
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }
}

if (!function_exists('requireRole')) {
    function requireRole($required_role) {
        requireLogin();
        if ($_SESSION['role'] !== $required_role) {
            header('Location: dashboard.php?error=access_denied');
            exit();
        }
    }
}

if (!function_exists('requireRoles')) {
    function requireRoles($allowed_roles) {
        requireLogin();
        if (!in_array($_SESSION['role'], $allowed_roles)) {
            header('Location: dashboard.php?error=access_denied');
            exit();
        }
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (!isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }
}

if (!function_exists('hasRole')) {
    function hasRole($role) {
        return isLoggedIn() && $_SESSION['role'] === $role;
    }
}

if (!function_exists('logout')) {
    function logout() {
        // Destroy all session data
        $_SESSION = array();
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
    }
}

if (!function_exists('regenerateSession')) {
    function regenerateSession() {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
    }
}

if (!function_exists('validateCSRF')) {
    function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('generateCSRF')) {
    function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// Rate limiting for login attempts
if (!function_exists('checkRateLimit')) {
    function checkRateLimit($identifier, $max_attempts = 5, $time_window = 900) { // 15 minutes
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        $now = time();
        $attempts = $_SESSION[$key];
        
        // Remove old attempts outside the time window
        $attempts = array_filter($attempts, function($timestamp) use ($now, $time_window) {
            return ($now - $timestamp) < $time_window;
        });
        
        $_SESSION[$key] = $attempts;
        
        return count($attempts) < $max_attempts;
    }
}

if (!function_exists('recordFailedAttempt')) {
    function recordFailedAttempt($identifier) {
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        $_SESSION[$key][] = time();
    }
}

if (!function_exists('clearFailedAttempts')) {
    function clearFailedAttempts($identifier) {
        $key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$key]);
    }
}
?>