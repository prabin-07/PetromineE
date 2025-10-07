<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login to access station details
if (!isLoggedIn()) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'station-details.php');
    header('Location: login.php?redirect=' . $redirect);
    exit();
}

$station_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$station = null;
$services = [];
$price_history = [];
$user_can_review = false;

if (!$station_id) {
    header('Location: index.php');
    exit();
}

try {
    // Get station details with current prices
    $query = "
        SELECT 
            fs.id,
            fs.name,
            fs.address,
            fs.latitude,
            fs.longitude,
            fs.phone,
            fs.is_active,
            fs.created_at,
            u.username as owner_name,
            MAX(CASE WHEN fp.fuel_type = 'petrol' THEN fp.price END) as petrol_price,
            MAX(CASE WHEN fp.fuel_type = 'diesel' THEN fp.price END) as diesel_price,
            MAX(CASE WHEN fp.fuel_type = 'petrol' THEN fp.effective_date END) as petrol_updated,
            MAX(CASE WHEN fp.fuel_type = 'diesel' THEN fp.effective_date END) as diesel_updated
        FROM fuel_stations fs
        LEFT JOIN users u ON fs.owner_id = u.id
        LEFT JOIN fuel_prices fp ON fs.id = fp.station_id
        WHERE fs.id = :station_id AND fs.is_active = 1
        GROUP BY fs.id, fs.name, fs.address, fs.latitude, fs.longitude, fs.phone, fs.is_active, fs.created_at, u.username
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':station_id', $station_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $error = 'Station not found or inactive';
    } else {
        $station = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get station services
        $serviceQuery = "
            SELECT service_name, is_available, price, description 
            FROM station_services 
            WHERE station_id = :station_id 
              AND service_name IN ('Air Filling', 'Restrooms')
            ORDER BY is_available DESC, service_name ASC
        ";
        $serviceStmt = $db->prepare($serviceQuery);
        $serviceStmt->bindParam(':station_id', $station_id, PDO::PARAM_INT);
        $serviceStmt->execute();
        $services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get price history for the last 30 days
        $historyQuery = "
            SELECT fuel_type, price, effective_date
            FROM fuel_prices 
            WHERE station_id = :station_id 
            AND effective_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY effective_date DESC, fuel_type
            LIMIT 20
        ";
        $historyStmt = $db->prepare($historyQuery);
        $historyStmt->bindParam(':station_id', $station_id, PDO::PARAM_INT);
        $historyStmt->execute();
        $price_history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check if user can review (logged in and not the owner)
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $user_can_review = $user['role'] === 'customer';
        }
    }
    
} catch (PDOException $e) {
    error_log("Station details error: " . $e->getMessage());
    $error = 'Error loading station details';
}

// Calculate distance (mock function - in real app you'd use user's location)
function calculateDistance($lat1, $lon1, $lat2 = 40.7128, $lon2 = -74.0060) {
    // Mock calculation - returns random distance between 1-10 km
    return round(rand(10, 100) / 10, 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $station ? htmlspecialchars($station['name']) : 'Station Details'; ?> - Petromine</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .station-hero {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
            color: white;
            padding: 2rem 0;
        }
        
        .station-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .station-info h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .station-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .station-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .price-display {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            min-width: 200px;
        }
        
        .price-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .price-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .fuel-type-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .current-price {
            font-size: 2rem;
            font-weight: 700;
            color: #27AE60;
            margin-bottom: 0.5rem;
        }
        
        .price-update {
            font-size: 0.8rem;
            color: #999;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .service-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #27AE60;
        }
        
        .service-card.unavailable {
            opacity: 0.6;
            border-left-color: #ccc;
        }
        
        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .service-name {
            font-weight: 600;
            color: #333;
        }
        
        .service-price {
            font-weight: 700;
            color: #667eea;
        }
        
        .service-status {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        
        .status-unavailable {
            background: #f8d7da;
            color: #721c24;
        }
        
        .price-history-chart {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        .map-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            margin: 2rem 0;
        }
        
        .mock-map {
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            height: 300px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.1rem;
            margin: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .station-header {
                flex-direction: column;
            }
            
            .price-grid {
                grid-template-columns: 1fr;
            }
            
            .station-actions {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><i class="fas fa-gas-pump"></i> Petromine</h2>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Home</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if ($error): ?>
        <main class="main-content">
            <div class="container">
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
                <div style="text-align: center; margin: 2rem 0;">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Stations
                    </a>
                </div>
            </div>
        </main>
    <?php else: ?>
        <!-- Station Hero Section -->
        <section class="station-hero">
            <div class="container">
                <div class="station-header">
                    <div class="station-info">
                        <h1><?php echo htmlspecialchars($station['name']); ?></h1>
                        <div class="station-meta">
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo calculateDistance($station['latitude'], $station['longitude']); ?> km away</span>
                            </div>
                            <?php if ($station['phone']): ?>
                                <div class="meta-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($station['phone']); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Open 24/7</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-star"></i>
                                <span>4.2 Rating</span>
                            </div>
                        </div>
                        <p style="opacity: 0.9; margin-bottom: 1rem;">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($station['address']); ?>
                        </p>
                    </div>
                    
                    <div class="station-actions">
                        <?php if (isLoggedIn()): ?>
                            <a href="save-to-buy.php?station_id=<?php echo $station['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-lock"></i> Save to Buy
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login to Save
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-secondary" onclick="getDirections()">
                            <i class="fas fa-directions"></i> Get Directions
                        </button>
                        <button class="btn btn-secondary" onclick="shareStation()">
                            <i class="fas fa-share"></i> Share Station
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <main class="main-content">
            <div class="container">
                <!-- Current Prices -->
                <section>
                    <h2><i class="fas fa-gas-pump"></i> Current Fuel Prices</h2>
                    <div class="price-grid">
                        <div class="price-card">
                            <div class="fuel-type-label">Petrol</div>
                            <div class="current-price">₹<?php echo $station['petrol_price'] ?: 'N/A'; ?></div>
                            <div class="price-update">
                                Updated: <?php echo $station['petrol_updated'] ? date('M j, g:i A', strtotime($station['petrol_updated'])) : 'Not available'; ?>
                            </div>
                            <?php if ($station['petrol_price']): ?>
                                <div style="margin-top: 1rem;">
                                    <span class="service-tag" style="background: #28a745; color: white;">
                                        <i class="fas fa-arrow-down"></i> ₹2.50 below average
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="price-card">
                            <div class="fuel-type-label">Diesel</div>
                            <div class="current-price">₹<?php echo $station['diesel_price'] ?: 'N/A'; ?></div>
                            <div class="price-update">
                                Updated: <?php echo $station['diesel_updated'] ? date('M j, g:i A', strtotime($station['diesel_updated'])) : 'Not available'; ?>
                            </div>
                            <?php if ($station['diesel_price']): ?>
                                <div style="margin-top: 1rem;">
                                    <span class="service-tag" style="background: #ffc107; color: #333;">
                                        <i class="fas fa-arrow-up"></i> ₹1.20 above average
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Services -->
                <section>
                    <h2><i class="fas fa-tools"></i> Available Services</h2>
                    <?php if (empty($services)): ?>
                        <div class="station-card" style="text-align: center;">
                            <i class="fas fa-info-circle" style="font-size: 2rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <p>No services information available for this station.</p>
                        </div>
                    <?php else: ?>
                        <div class="services-grid">
                            <?php foreach ($services as $service): ?>
                                <div class="service-card <?php echo !$service['is_available'] ? 'unavailable' : ''; ?>">
                                    <div class="service-header">
                                        <div class="service-name"><?php echo htmlspecialchars($service['service_name']); ?></div>
                                        <div class="service-status <?php echo $service['is_available'] ? 'status-available' : 'status-unavailable'; ?>">
                                            <?php echo $service['is_available'] ? 'Available' : 'Unavailable'; ?>
                                        </div>
                                    </div>
                                    <?php if ($service['price'] > 0): ?>
                                        <div class="service-price">₹<?php echo number_format($service['price'], 2); ?></div>
                                    <?php endif; ?>
                                    <?php if ($service['description']): ?>
                                        <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                                            <?php echo htmlspecialchars($service['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Price History -->
                <section>
                    <h2><i class="fas fa-chart-line"></i> Recent Price History</h2>
                    <div class="price-history-chart">
                        <?php if (empty($price_history)): ?>
                            <div style="text-align: center; color: #666;">
                                <i class="fas fa-chart-line" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>No price history available</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($price_history as $history): ?>
                                <div class="history-item">
                                    <div>
                                        <span style="font-weight: 600; text-transform: capitalize;">
                                            <?php echo $history['fuel_type']; ?>
                                        </span>
                                        <span style="color: #666; margin-left: 1rem;">
                                            <?php echo date('M j, Y g:i A', strtotime($history['effective_date'])); ?>
                                        </span>
                                    </div>
                                    <div style="font-weight: 700; color: #667eea;">
                                        ₹<?php echo $history['price']; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Location & Map -->
                <section>
                    <h2><i class="fas fa-map-marked-alt"></i> Location & Directions</h2>
                    <div class="map-container">
                        <div class="mock-map">
                            <div>
                                <i class="fas fa-map-marker-alt" style="font-size: 2rem; margin-bottom: 1rem; color: #27AE60;"></i>
                                <p>Interactive Map Coming Soon</p>
                                <p style="font-size: 0.9rem; opacity: 0.7;">
                                    Coordinates: <?php echo $station['latitude']; ?>, <?php echo $station['longitude']; ?>
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <button class="btn btn-primary" onclick="getDirections()">
                                <i class="fas fa-directions"></i> Get Directions
                            </button>
                            <button class="btn btn-secondary" onclick="callStation()">
                                <i class="fas fa-phone"></i> Call Station
                            </button>
                            <button class="btn btn-secondary" onclick="reportIssue()">
                                <i class="fas fa-flag"></i> Report Issue
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Reviews Section -->
                <?php if ($user_can_review): ?>
                <section>
                    <h2><i class="fas fa-star"></i> Customer Reviews</h2>
                    <div class="station-card">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <div style="font-size: 3rem; color: #ffc107; margin-bottom: 0.5rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">4.2 out of 5</div>
                            <div style="color: #666;">Based on 127 reviews</div>
                        </div>
                        
                        <div style="margin-bottom: 2rem;">
                            <button class="btn btn-primary" onclick="writeReview()">
                                <i class="fas fa-edit"></i> Write a Review
                            </button>
                        </div>
                        
                        <!-- Sample Reviews -->
                        <div style="border-top: 1px solid #eee; padding-top: 2rem;">
                            <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #f0f0f0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: 600;">Rajesh Kumar</div>
                                    <div style="color: #ffc107;">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div style="color: #666; font-size: 0.9rem; margin-bottom: 0.5rem;">2 days ago</div>
                                <p style="margin: 0;">Great service and competitive prices. The staff is friendly and the station is always clean.</p>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: 600;">Priya Sharma</div>
                                    <div style="color: #ffc107;">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <div style="color: #666; font-size: 0.9rem; margin-bottom: 0.5rem;">1 week ago</div>
                                <p style="margin: 0;">Good location and decent prices. The car wash service is excellent. Would recommend!</p>
                            </div>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            </div>
        </main>
    <?php endif; ?>

    <script src="assets/js/alerts.js"></script>
    <script>
        function getDirections() {
            const address = "<?php echo addslashes($station['address'] ?? ''); ?>";
            showToast('Opening directions in your default map app...', 'info', 'Navigation');
            
            // In a real app, this would open the user's preferred map app
            setTimeout(() => {
                showToast('Feature coming soon! Address copied to clipboard.', 'info', 'Coming Soon');
                
                // Copy address to clipboard
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(address);
                }
            }, 1000);
        }
        
        function shareStation() {
            const stationName = "<?php echo addslashes($station['name'] ?? ''); ?>";
            const url = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: `${stationName} - Petromine`,
                    text: `Check out current fuel prices at ${stationName}`,
                    url: url
                });
            } else {
                // Fallback - copy to clipboard
                navigator.clipboard.writeText(url).then(() => {
                    showToast('Station link copied to clipboard!', 'success', 'Shared');
                });
            }
        }
        
        function callStation() {
            const phone = "<?php echo $station['phone'] ?? ''; ?>";
            if (phone) {
                window.location.href = `tel:${phone}`;
            } else {
                showToast('Phone number not available for this station', 'warning', 'No Contact');
            }
        }
        
        function reportIssue() {
            showConfirm({
                title: 'Report an Issue',
                message: 'What type of issue would you like to report about this station?',
                confirmText: 'Continue',
                cancelText: 'Cancel',
                onConfirm: () => {
                    showToast('Thank you for your report. We will investigate this issue.', 'success', 'Report Submitted');
                }
            });
        }
        
        function writeReview() {
            <?php if (!isLoggedIn()): ?>
                showConfirm({
                    title: 'Login Required',
                    message: 'You need to login to write a review. Would you like to go to the login page?',
                    confirmText: 'Login',
                    cancelText: 'Cancel',
                    onConfirm: () => {
                        window.location.href = 'login.php';
                    }
                });
            <?php else: ?>
                showToast('Review feature coming soon! Thank you for your interest.', 'info', 'Coming Soon');
            <?php endif; ?>
        }
        
        // Show welcome message
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                showToast('Station details loaded! Use "Save to Buy" to lock in current prices.', 'info', 'Tip', 5000);
            }, 1000);
        });
    </script>
</body>
</html>