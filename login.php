<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isLoggedIn()) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
    header('Location: ' . $redirect);
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!validateCSRF($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // Check rate limiting
        $client_ip = $_SERVER['REMOTE_ADDR'];
        $rate_limit_key = $email . '_' . $client_ip;
        
        if (!checkRateLimit($rate_limit_key)) {
            $error = 'Too many login attempts. Please try again in 15 minutes.';
        } else {
            try {
                // Query user from database
                $query = "SELECT id, username, email, password, role, created_at FROM users WHERE email = :email AND id > 0";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Clear failed attempts
                        clearFailedAttempts($rate_limit_key);
                        
                        // Regenerate session ID for security
                        regenerateSession();
                        
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['login_time'] = time();
                        
                        // Update last login time
                        $updateQuery = "UPDATE users SET updated_at = NOW() WHERE id = :id";
                        $updateStmt = $db->prepare($updateQuery);
                        $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                        $updateStmt->execute();
                        
                        // Redirect to intended page or dashboard
                        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
                        header('Location: ' . $redirect);
                        exit();
                    } else {
                        recordFailedAttempt($rate_limit_key);
                        $error = 'Invalid email or password';
                    }
                } else {
                    recordFailedAttempt($rate_limit_key);
                    $error = 'Invalid email or password';
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

// Handle success message from registration
if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success = 'Registration successful! Please login with your credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Petromine</title>
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
                <a href="register.php" class="nav-link">Register</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Login to Petromine</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" required
                               autocomplete="current-password">
                        <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="remember" name="remember" style="margin-right: 8px;">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div style="text-align: center; margin: 1rem 0;">
                <a href="forgot-password.php" class="link-primary">Forgot Password?</a>
            </div>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php" class="link-primary">Register here</a>
            </p>
            
            <!-- Demo Credentials Helper -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 0.9rem;">
                <h4 style="margin: 0 0 10px 0; color: #333;">Demo Credentials:</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button type="button" class="demo-login" data-email="customer@demo.com" data-password="password123" 
                            style="padding: 8px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">
                        <strong>Customer</strong><br>
                        <small>customer@demo.com</small>
                    </button>
                    <button type="button" class="demo-login" data-email="owner@demo.com" data-password="password123"
                            style="padding: 8px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">
                        <strong>Pump Owner</strong><br>
                        <small>owner@demo.com</small>
                    </button>
                    <button type="button" class="demo-login" data-email="admin@demo.com" data-password="password123"
                            style="padding: 8px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">
                        <strong>Admin</strong><br>
                        <small>admin@demo.com</small>
                    </button>
                    <button type="button" class="demo-login" data-email="admin@petromine.com" data-password="password"
                            style="padding: 8px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">
                        <strong>Super Admin</strong><br>
                        <small>admin@petromine.com</small>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/alerts.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                password.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        });

        // Demo login functionality with enhanced feedback
        document.querySelectorAll('.demo-login').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('email').value = this.dataset.email;
                document.getElementById('password').value = this.dataset.password;
                
                // Show toast notification
                const role = this.querySelector('strong').textContent;
                showToast(`Demo credentials loaded for ${role}`, 'info', 'Demo Login', 3000);
            });
        });

        // Enhanced form validation and loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                showToast('Please fill in all fields', 'warning', 'Validation Error');
                return;
            }
            
            if (!ValidationRules.email(email)) {
                e.preventDefault();
                showToast('Please enter a valid email address', 'warning', 'Invalid Email');
                return;
            }
            
            // Show loading state
            loginBtn.innerHTML = '<div class="spinner"></div> Logging in...';
            loginBtn.disabled = true;
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(() => {
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
                loginBtn.disabled = false;
            }, 10000);
        });

        // Show welcome message for successful registration
        <?php if ($success): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('<?php echo addslashes($success); ?>', 'success', 'Welcome!', 8000);
            });
        <?php endif; ?>
    </script>
</body>
</html>