<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Handle logout message
$logged_out = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petromine - Real-time Fuel Prices</title>
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
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <span class="nav-link">Hello, <?php echo htmlspecialchars(getCurrentUser()['username']); ?></span>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <?php if ($logged_out): ?>
            <div class="container">
                <div class="alert alert-success" style="margin: 20px 0;">
                    <i class="fas fa-check-circle"></i> You have been successfully logged out.
                </div>
            </div>
        <?php endif; ?>
        
        <section class="hero">
            <div class="hero-content">
                <h1>Real-time Fuel Prices at Your Fingertips</h1>
                <p>Monitor petrol and diesel prices, discover services, and save money with our "Save to Buy" feature</p>
                <div class="hero-buttons">
                    <a href="#stations" class="btn btn-primary">View Stations</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-secondary">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="stations" class="stations-section">
            <div class="container">
                <h2>Nearby Fuel Stations</h2>
                <div class="stations-grid" id="stationsGrid">
                    <!-- Stations will be loaded here via JavaScript -->
                </div>
            </div>
        </section>
    </main>

    <script>
        window.userLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        <?php if (isLoggedIn()): ?>
        window.userRole = '<?php echo getCurrentUser()['role']; ?>';
        <?php endif; ?>
    </script>
    <script src="assets/js/alerts.js"></script>
    <script src="assets/js/main.js"></script>
    
    <?php if ($logged_out): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('You have been successfully logged out. Thank you for using Petromine!', 'success', 'Logged Out', 5000);
        });
    </script>
    <?php endif; ?>
</body>
</html>