<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require admin role to access this page
requireRole('admin');

$user = getCurrentUser();

// Get system statistics
try {
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total_users FROM users");
    $total_users = $stmt->fetchColumn();
    
    // Total stations
    $stmt = $db->query("SELECT COUNT(*) as total_stations FROM fuel_stations");
    $total_stations = $stmt->fetchColumn();
    
    // Total transactions
    $stmt = $db->query("SELECT COUNT(*) as total_transactions FROM save_to_buy");
    $total_transactions = $stmt->fetchColumn();
    
    // Active transactions
    $stmt = $db->query("SELECT COUNT(*) as active_transactions FROM save_to_buy WHERE status = 'active'");
    $active_transactions = $stmt->fetchColumn();
    
    // Recent users
    $stmt = $db->query("SELECT username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent transactions
    $stmt = $db->query("
        SELECT stb.*, u.username, fs.name as station_name 
        FROM save_to_buy stb 
        JOIN users u ON stb.user_id = u.id 
        JOIN fuel_stations fs ON stb.station_id = fs.id 
        ORDER BY stb.created_at DESC 
        LIMIT 10
    ");
    $recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Admin panel error: " . $e->getMessage());
    $error = "Error loading admin data";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Petromine</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><i class="fas fa-gas-pump"></i> Petromine Admin</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <span class="nav-link">Admin: <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="dashboard">
            <div class="container">
                <div class="dashboard-header">
                    <h1><i class="fas fa-shield-alt"></i> System Administration</h1>
                    <p>Manage users, stations, and monitor platform activity</p>
                </div>

                <!-- System Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_stations; ?></div>
                        <div class="stat-label">Fuel Stations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_transactions; ?></div>
                        <div class="stat-label">Total Transactions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $active_transactions; ?></div>
                        <div class="stat-label">Active Locks</div>
                    </div>
                </div>

                <!-- Recent Users -->
                <section style="margin: 3rem 0;">
                    <h2><i class="fas fa-users"></i> Recent Users</h2>
                    <div class="station-card">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Username</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Email</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Role</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $recent_user): ?>
                                        <tr>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo htmlspecialchars($recent_user['username']); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo htmlspecialchars($recent_user['email']); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <span class="service-tag" style="background: 
                                                    <?php echo $recent_user['role'] === 'admin' ? '#dc3545' : 
                                                              ($recent_user['role'] === 'pump_owner' ? '#fd7e14' : '#28a745'); ?>; 
                                                    color: white;">
                                                    <?php echo ucfirst(str_replace('_', ' ', $recent_user['role'])); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo date('M j, Y', strtotime($recent_user['created_at'])); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Recent Transactions -->
                <section style="margin: 3rem 0;">
                    <h2><i class="fas fa-exchange-alt"></i> Recent Transactions</h2>
                    <div class="station-card">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">User</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Station</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Fuel</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Amount</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Status</th>
                                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                        <tr>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo htmlspecialchars($transaction['username']); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo htmlspecialchars($transaction['station_name']); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo ucfirst($transaction['fuel_type']); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                ₹<?php echo number_format($transaction['total_amount'], 2); ?>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <span class="service-tag" style="background: 
                                                    <?php echo $transaction['status'] === 'active' ? '#28a745' : 
                                                              ($transaction['status'] === 'redeemed' ? '#007bff' : '#6c757d'); ?>; 
                                                    color: white;">
                                                    <?php echo ucfirst($transaction['status']); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">
                                                <?php echo date('M j, Y', strtotime($transaction['created_at'])); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section style="margin: 3rem 0;">
                    <h2><i class="fas fa-tools"></i> Quick Actions</h2>
                    <div class="stations-grid">
                        <div class="station-card" style="text-align: center;">
                            <h3><i class="fas fa-users-cog"></i> User Management</h3>
                            <p>Manage user accounts and permissions</p>
                            <button class="btn btn-primary" onclick="alert('User management feature coming soon!')">
                                Manage Users
                            </button>
                        </div>
                        <div class="station-card" style="text-align: center;">
                            <h3><i class="fas fa-gas-pump"></i> Station Management</h3>
                            <p>Monitor and manage fuel stations</p>
                            <button class="btn btn-primary" onclick="alert('Station management feature coming soon!')">
                                Manage Stations
                            </button>
                        </div>
                        <div class="station-card" style="text-align: center;">
                            <h3><i class="fas fa-chart-bar"></i> Analytics</h3>
                            <p>View detailed platform analytics</p>
                            <button class="btn btn-primary" onclick="alert('Analytics dashboard coming soon!')">
                                View Analytics
                            </button>
                        </div>
                        <div class="station-card" style="text-align: center;">
                            <h3><i class="fas fa-cog"></i> System Settings</h3>
                            <p>Configure platform settings</p>
                            <button class="btn btn-primary" onclick="alert('System settings coming soon!')">
                                Settings
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>