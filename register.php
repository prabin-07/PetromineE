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

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!validateCSRF($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!in_array($role, ['customer', 'pump_owner'])) {
        $error = 'Please select a valid account type';
    } else {
        // Check password strength
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $error = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        } else {
            try {
                // Check if email already exists
                $query = "SELECT id FROM users WHERE email = :email";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $error = 'An account with this email already exists';
                } else {
                    // Check if username already exists
                    $query = "SELECT id FROM users WHERE username = :username";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        $error = 'Username is already taken';
                    } else {
                        // Create new user
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $query = "INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES (:username, :email, :password, :role, NOW(), NOW())";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                        
                        if ($stmt->execute()) {
                            // Registration successful - redirect to login
                            header('Location: login.php?registered=1');
                            exit();
                        } else {
                            $error = 'Registration failed. Please try again.';
                        }
                    }
                }
            } catch (PDOException $e) {
                error_log("Registration error: " . $e->getMessage());
                $error = 'An error occurred during registration. Please try again later.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Petromine</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Join Petromine</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required 
                           minlength="3" maxlength="50"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           autocomplete="username">
                    <small style="color: #666;">At least 3 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="role">Account Type</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Select Account Type</option>
                        <option value="customer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                        <option value="pump_owner" <?php echo (isset($_POST['role']) && $_POST['role'] == 'pump_owner') ? 'selected' : ''; ?>>Petrol Pump Owner</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" required
                               minlength="6" autocomplete="new-password">
                        <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <small style="color: #666;">At least 6 characters with uppercase, lowercase, and number</small>
                    <div id="passwordStrength" style="margin-top: 5px;"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required
                           autocomplete="new-password">
                    <div id="passwordMatch" style="margin-top: 5px;"></div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="terms" required style="margin-right: 8px;">
                        I agree to the <a href="#" class="link-primary">Terms of Service</a> and <a href="#" class="link-primary">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="login.php" class="link-primary">Login here</a>
            </p>
        </div>
    </main>

    <script src="assets/js/alerts.js"></script>
    <script>
        // Initialize form validator
        const validator = new FormValidator(document.getElementById('registerForm'));
        
        // Add validation rules
        validator
            .addRule('username', ValidationRules.required, 'Username is required')
            .addRule('username', ValidationRules.minLength(3), 'Username must be at least 3 characters')
            .addRule('email', ValidationRules.required, 'Email is required')
            .addRule('email', ValidationRules.email, 'Please enter a valid email address')
            .addRule('password', ValidationRules.required, 'Password is required')
            .addRule('password', ValidationRules.minLength(6), 'Password must be at least 6 characters')
            .addRule('password', (value) => /(?=.*[a-z])/.test(value), 'Password must contain a lowercase letter')
            .addRule('password', (value) => /(?=.*[A-Z])/.test(value), 'Password must contain an uppercase letter')
            .addRule('password', (value) => /(?=.*\d)/.test(value), 'Password must contain a number')
            .addRule('confirm_password', ValidationRules.required, 'Please confirm your password')
            .addRule('confirm_password', ValidationRules.match('password'), 'Passwords do not match')
            .addRule('role', ValidationRules.required, 'Please select an account type');

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

        // Enhanced password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 6) strength++;
            else feedback.push('At least 6 characters');
            
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('Lowercase letter');
            
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('Uppercase letter');
            
            if (/\d/.test(password)) strength++;
            else feedback.push('Number');
            
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
            const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            
            if (password.length > 0) {
                strengthDiv.innerHTML = `
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${strength * 20}%; background: ${colors[strength-1] || '#dc3545'};"></div>
                    </div>
                    <small style="color: ${colors[strength-1] || '#dc3545'};">
                        <i class="fas fa-shield-alt"></i> ${labels[strength-1] || 'Very Weak'}
                    </small>
                `;
            } else {
                strengthDiv.innerHTML = '';
            }
        });

        // Enhanced password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchDiv.innerHTML = '<small class="valid-feedback"><i class="fas fa-check"></i> Passwords match</small>';
                } else {
                    matchDiv.innerHTML = '<small class="invalid-feedback"><i class="fas fa-times"></i> Passwords do not match</small>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });

        // Enhanced form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const registerBtn = document.getElementById('registerBtn');
            
            // Show loading state
            registerBtn.innerHTML = '<div class="spinner"></div> Creating Account...';
            registerBtn.disabled = true;
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(() => {
                registerBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register';
                registerBtn.disabled = false;
            }, 10000);
        });

        // Role selection helper
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const descriptions = {
                customer: 'As a customer, you can view fuel prices, save money with price locks, and track your savings.',
                pump_owner: 'As a pump owner, you can manage your fuel stations, update prices, and track customer activity.'
            };
            
            if (role && descriptions[role]) {
                showToast(descriptions[role], 'info', 'Account Type Info', 5000);
            }
        });
    </script>
</body>
</html>