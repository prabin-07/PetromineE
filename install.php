<?php
// Petromine Installation Script
// Run this file once to set up the database automatically

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'petromine';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Petromine Database Installation</h2>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>";
    
    // Create database
    echo "<p>✓ Creating database '$database'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    $pdo->exec("USE $database");
    
    // Read and execute schema
    echo "<p>✓ Setting up database schema...</p>";
    $schema = file_get_contents('database/schema.sql');
    $statements = explode(';', $schema);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    // Read and execute sample data
    echo "<p>✓ Inserting sample data...</p>";
    $sampleData = file_get_contents('database/sample_data.sql');
    $statements = explode(';', $sampleData);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(USE|--)/i', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip duplicate entries
                if ($e->getCode() != 23000) {
                    throw $e;
                }
            }
        }
    }
    
    // Read and execute demo users
    echo "<p>✓ Creating demo users...</p>";
    $demoUsers = file_get_contents('database/demo_users.sql');
    $statements = explode(';', $demoUsers);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(USE|--)/i', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip duplicate entries
                if ($e->getCode() != 23000) {
                    throw $e;
                }
            }
        }
    }
    
    // Read and execute price history
    echo "<p>✓ Adding price history data...</p>";
    $priceHistory = file_get_contents('database/price_history.sql');
    $statements = explode(';', $priceHistory);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(USE|--)/i', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip duplicate entries
                if ($e->getCode() != 23000) {
                    throw $e;
                }
            }
        }
    }
    
    // Get statistics
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM fuel_stations");
    $stationCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM fuel_prices");
    $priceCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM station_services");
    $serviceCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM save_to_buy");
    $purchaseCount = $stmt->fetchColumn();
    
    echo "<h3 style='color: green;'>✅ Installation Completed Successfully!</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>Database Statistics:</h4>";
    echo "<ul>";
    echo "<li><strong>Users:</strong> $userCount (including customers, pump owners, and admins)</li>";
    echo "<li><strong>Fuel Stations:</strong> $stationCount (with real addresses and services)</li>";
    echo "<li><strong>Price Records:</strong> $priceCount (including historical data)</li>";
    echo "<li><strong>Station Services:</strong> $serviceCount (various services across stations)</li>";
    echo "<li><strong>Save-to-Buy Records:</strong> $purchaseCount (sample transactions)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>🔑 Demo Login Credentials:</h4>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'><th style='padding: 8px; border: 1px solid #ddd;'>Role</th><th style='padding: 8px; border: 1px solid #ddd;'>Email</th><th style='padding: 8px; border: 1px solid #ddd;'>Password</th></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'>Admin</td><td style='padding: 8px; border: 1px solid #ddd;'>admin@petromine.com</td><td style='padding: 8px; border: 1px solid #ddd;'>password</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'>Demo Admin</td><td style='padding: 8px; border: 1px solid #ddd;'>admin@demo.com</td><td style='padding: 8px; border: 1px solid #ddd;'>password123</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'>Customer</td><td style='padding: 8px; border: 1px solid #ddd;'>customer@demo.com</td><td style='padding: 8px; border: 1px solid #ddd;'>password123</td></tr>";
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'>Pump Owner</td><td style='padding: 8px; border: 1px solid #ddd;'>owner@demo.com</td><td style='padding: 8px; border: 1px solid #ddd;'>password123</td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>🚀 Next Steps:</h4>";
    echo "<ol>";
    echo "<li><strong>Update Database Configuration:</strong> Edit <code>config/database.php</code> if your database credentials are different</li>";
    echo "<li><strong>Access the Application:</strong> <a href='index.php' style='color: #007bff;'>Go to Petromine Homepage</a></li>";
    echo "<li><strong>Login as Demo User:</strong> <a href='login.php' style='color: #007bff;'>Login Page</a></li>";
    echo "<li><strong>Explore Features:</strong> Try the Save-to-Buy feature, view station details, and manage stations</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h4>⚠️ Security Note:</h4>";
    echo "<p>For production use:</p>";
    echo "<ul>";
    echo "<li>Change all default passwords</li>";
    echo "<li>Update database credentials in <code>config/database.php</code></li>";
    echo "<li>Remove or secure this installation file</li>";
    echo "<li>Configure proper web server security</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p style='text-align: center; margin-top: 30px;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🏠 Go to Petromine</a> ";
    echo "<a href='login.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-left: 10px;'>🔐 Login</a>";
    echo "</p>";
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #dc3545; border-radius: 8px; background: #f8d7da; color: #721c24;'>";
    echo "<h2>❌ Installation Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h4>Common Solutions:</h4>";
    echo "<ul>";
    echo "<li>Make sure MySQL server is running</li>";
    echo "<li>Check database credentials in this file</li>";
    echo "<li>Ensure the MySQL user has CREATE DATABASE privileges</li>";
    echo "<li>Verify PHP PDO MySQL extension is installed</li>";
    echo "</ul>";
    echo "</div>";
}
?>