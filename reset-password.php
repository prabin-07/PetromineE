<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';
$token = trim($_GET['token'] ?? '');
$valid_token = false;
$token_email = '';

// Validate token
if ($token) {
    try {
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW() AND used = 0");
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $valid_token = true;
            $token_email = $row['email'];
        } else {
            $error = 'This reset link is invalid or has expired. Please request a new one.';
        }
    } catch (PDOException $e) {
        $error = 'An error occurred. Please try again.';
    }
} else {
    $error = 'No reset token provided.';
}

// Handle password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!validateCSRF($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE users SET password = :password WHERE email = :email");
            $updateStmt->execute([':password' => $hashed, ':email' => $token_email]);

            // Mark token as used
            $db->prepare("UPDATE password_resets SET used = 1 WHERE token = :token")->execute([':token' => $token]);

            $success = 'Your password has been reset successfully. You can now login.';
            $valid_token = false;
        } catch (PDOException $e) {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Petromine</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link type="image/x-icon" rel="icon" href="assets/img/logo.png">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><i class="fas fa-gas-pump"></i> Petromine</h2>
            </div>
            <div class="nav-menu">
                <a href="login.php" class="nav-link">Login</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">
                <i class="fas fa-lock"></i> Reset Password
            </h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="forgot-password.php" class="btn btn-primary">Request New Link</a>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                </div>
            <?php elseif ($valid_token): ?>
                <form method="POST" id="resetForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control"
                               required minlength="8" autocomplete="new-password">
                        <small style="color: #666;">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               class="form-control" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="resetBtn">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/js/alerts.js"></script>
    <script>
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            if (pwd !== confirm) {
                e.preventDefault();
                showToast('Passwords do not match.', 'error', 'Validation Error');
                return;
            }
            const btn = document.getElementById('resetBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
