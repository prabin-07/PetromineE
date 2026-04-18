<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!validateCSRF($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Create password_resets table if it doesn't exist
            $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) NOT NULL,
                token VARCHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                used TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY idx_token (token),
                KEY idx_email (email)
            )");

            // Check if email exists
            $query = "SELECT id, username FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Delete any existing tokens for this email
                $db->prepare("DELETE FROM password_resets WHERE email = :email")->execute([':email' => $email]);
                
                // Generate reset token
                $reset_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token
                $insertToken = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                $insertToken->execute([':email' => $email, ':token' => $reset_token, ':expires_at' => $expires_at]);
                
                // Build reset link
                $reset_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/reset-password.php?token=' . $reset_token;
                
                // Store in session for demo (in production, send via email)
                $_SESSION['demo_reset_link'] = $reset_link;
                $_SESSION['demo_reset_user'] = $user['username'];
            }
            
            // Always show same message for security
            $success = 'If an account with that email exists, you will receive password reset instructions shortly.';
            
        } catch (PDOException $e) {
            error_log("Forgot password error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Petromine</title>
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
                <a href="index.php" class="nav-link">Home</a>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">
                <i class="fas fa-key"></i> Forgot Password
            </h2>
            
            <p style="text-align: center; color: #666; margin-bottom: 2rem;">
                Enter your email address and we'll send you instructions to reset your password.
            </p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php if (isset($_SESSION['demo_reset_link'])): ?>
                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 1rem; font-size: 0.9rem;">
                    <strong style="color: #856404;">Demo Mode - Reset Link:</strong><br>
                    <a href="<?php echo htmlspecialchars($_SESSION['demo_reset_link']); ?>" style="word-break: break-all; color: #0d6efd;">
                        <?php echo htmlspecialchars($_SESSION['demo_reset_link']); ?>
                    </a>
                    <p style="margin: 0.5rem 0 0; color: #856404; font-size: 0.85rem;">In production, this link would be sent to the user's email.</p>
                </div>
                <?php unset($_SESSION['demo_reset_link'], $_SESSION['demo_reset_user']); ?>
                <?php endif; ?>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                </div>
            <?php else: ?>
                <form method="POST" id="forgotForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               autocomplete="email">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;" id="forgotBtn">
                        <i class="fas fa-paper-plane"></i> Send Reset Instructions
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="login.php" class="link-primary">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
                
                <!-- Demo Note -->
                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 0.9rem;">
                    <h4 style="margin: 0 0 10px 0; color: #856404;">Demo Note:</h4>
                    <p style="margin: 0; color: #856404;">
                        This is a demo application. In a production environment, this would send an actual email with reset instructions.
                        For demo purposes, you can use the demo credentials on the login page.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Form validation and loading state
        document.getElementById('forgotForm')?.addEventListener('submit', function() {
            const forgotBtn = document.getElementById('forgotBtn');
            forgotBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            forgotBtn.disabled = true;
        });
    </script>
</body>
</html>