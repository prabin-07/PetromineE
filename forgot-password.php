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
            // Check if email exists
            $query = "SELECT id, username FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Generate reset token
                $reset_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token in database (you would need to create this table)
                // For demo purposes, we'll just show a success message
                $success = 'If an account with that email exists, you will receive password reset instructions shortly.';
                
                // In a real application, you would:
                // 1. Store the reset token in a password_resets table
                // 2. Send an email with the reset link
                // 3. Create a reset-password.php page to handle the token
                
            } else {
                // Don't reveal if email exists or not for security
                $success = 'If an account with that email exists, you will receive password reset instructions shortly.';
            }
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