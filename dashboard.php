<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login to access dashboard
requireLogin();

$user = getCurrentUser();
$user_role = $user['role'];

// Handle access denied error
$access_denied = isset($_GET['error']) && $_GET['error'] === 'access_denied';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Petromine</title>
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
                <?php if ($user_role == 'pump_owner'): ?>
                    <a href="manage-station.php" class="nav-link">Manage Station</a>
                <?php elseif ($user_role == 'admin'): ?>
                    <a href="admin.php" class="nav-link">Admin Panel</a>
                <?php endif; ?>
                <span class="nav-link">Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="dashboard">
            <div class="container">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</p>
                    <?php if ($access_denied): ?>
                        <div class="alert alert-error">Access denied. You don't have permission to access that page.</div>
                    <?php endif; ?>
                </div>

                <?php if ($user_role == 'customer'): ?>
                    <!-- Customer Dashboard -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" id="totalSavings">₹0.00</div>
                            <div class="stat-label">Total Savings</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="activeLocks">0</div>
                            <div class="stat-label">Active Price Locks</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="totalPurchases">0</div>
                            <div class="stat-label">Total Purchases</div>
                        </div>
                    </div>

                    <section>
                        <h2>Your Saved Purchases</h2>
                        <div class="stations-grid" id="savedPurchases">
                            <!-- Saved purchases will be loaded here -->
                        </div>
                    </section>

                <?php elseif ($user_role == 'pump_owner'): ?>
                    <!-- Pump Owner Dashboard -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" id="totalStations">0</div>
                            <div class="stat-label">Your Stations</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="todaysSales">₹0.00</div>
                            <div class="stat-label">Today's Sales</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="activeLocks">0</div>
                            <div class="stat-label">Active Customer Locks</div>
                        </div>
                    </div>

                    <div style="text-align: center; margin: 2rem 0;">
                        <a href="manage-station.php" class="btn btn-primary">
                            <i class="fas fa-cog"></i> Manage Your Stations
                        </a>
                    </div>

                <?php elseif ($user_role == 'admin'): ?>
                    <!-- Admin Dashboard -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" id="totalUsers">0</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="totalStations">0</div>
                            <div class="stat-label">Total Stations</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="totalTransactions">0</div>
                            <div class="stat-label">Total Transactions</div>
                        </div>
                    </div>

                    <div style="text-align: center; margin: 2rem 0;">
                        <a href="admin.php" class="btn btn-primary">
                            <i class="fas fa-shield-alt"></i> Admin Panel
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        window.userLoggedIn = true;
        window.userRole = '<?php echo $user_role; ?>';
    </script>
    <script src="assets/js/alerts.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Welcome message for first-time users
            const welcomeShown = localStorage.getItem('welcomeShown');
            if (!welcomeShown) {
                setTimeout(() => {
                    const role = '<?php echo $user_role; ?>';
                    const messages = {
                        customer: 'Welcome to Petromine! Start saving money by locking in fuel prices with our Save to Buy feature.',
                        pump_owner: 'Welcome to Petromine! Manage your fuel stations and update prices to attract more customers.',
                        admin: 'Welcome to Petromine Admin Panel! Monitor platform activity and manage users from here.'
                    };
                    
                    showToast(messages[role] || 'Welcome to Petromine!', 'info', 'Welcome!', 8000);
                    localStorage.setItem('welcomeShown', 'true');
                }, 1000);
            }
            
            <?php if ($access_denied): ?>
            showToast('Access denied. You don\'t have permission to access that page.', 'error', 'Access Denied', 6000);
            <?php endif; ?>
        });
    </script>
</body>
</html>