<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require pump owner role to access this page
requireRole('pump_owner');

$user = getCurrentUser();
$user_id = $user['id'];
$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_station':
                $name = trim($_POST['name']);
                $address = trim($_POST['address']);
                $phone = trim($_POST['phone']);
                $latitude = (float)$_POST['latitude'];
                $longitude = (float)$_POST['longitude'];
                
                if ($name && $address) {
                    $query = "INSERT INTO fuel_stations (owner_id, name, address, phone, latitude, longitude) VALUES (:owner_id, :name, :address, :phone, :latitude, :longitude)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':owner_id', $user_id);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':address', $address);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':latitude', $latitude);
                    $stmt->bindParam(':longitude', $longitude);
                    
                    if ($stmt->execute()) {
                        $message = 'Station added successfully!';
                    } else {
                        $error = 'Failed to add station';
                    }
                }
                break;
                
            case 'update_price':
                $station_id = (int)$_POST['station_id'];
                $fuel_type = $_POST['fuel_type'];
                $price = (float)$_POST['price'];
                
                if ($station_id && $fuel_type && $price > 0) {
                    // Verify station ownership
                    $verifyQuery = "SELECT id FROM fuel_stations WHERE id = :station_id AND owner_id = :owner_id";
                    $verifyStmt = $db->prepare($verifyQuery);
                    $verifyStmt->bindParam(':station_id', $station_id);
                    $verifyStmt->bindParam(':owner_id', $user_id);
                    $verifyStmt->execute();
                    
                    if ($verifyStmt->rowCount() > 0) {
                        $query = "INSERT INTO fuel_prices (station_id, fuel_type, price) VALUES (:station_id, :fuel_type, :price)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':station_id', $station_id);
                        $stmt->bindParam(':fuel_type', $fuel_type);
                        $stmt->bindParam(':price', $price);
                        
                        if ($stmt->execute()) {
                            $message = 'Price updated successfully!';
                        } else {
                            $error = 'Failed to update price';
                        }
                    } else {
                        $error = 'Station not found or access denied';
                    }
                }
                break;
        }
    }
}

// Get user's stations
$stationsQuery = "
    SELECT 
        fs.id,
        fs.name,
        fs.address,
        fs.phone,
        fs.is_active,
        MAX(CASE WHEN fp.fuel_type = 'petrol' THEN fp.price END) as petrol_price,
        MAX(CASE WHEN fp.fuel_type = 'diesel' THEN fp.price END) as diesel_price
    FROM fuel_stations fs
    LEFT JOIN fuel_prices fp ON fs.id = fp.station_id
    WHERE fs.owner_id = :owner_id
    GROUP BY fs.id, fs.name, fs.address, fs.phone, fs.is_active
    ORDER BY fs.name
";

$stmt = $db->prepare($stationsQuery);
$stmt->bindParam(':owner_id', $user_id);
$stmt->execute();
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stations - Petromine</title>
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <h1 style="text-align: center; margin: 2rem 0;">Manage Your Stations</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Add New Station -->
            <div class="station-card" style="margin-bottom: 3rem;">
                <h2><i class="fas fa-plus"></i> Add New Station</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_station">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="name">Station Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="number" id="latitude" name="latitude" class="form-control" step="0.000001">
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="number" id="longitude" name="longitude" class="form-control" step="0.000001">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Station
                    </button>
                </form>
            </div>
            
            <!-- Existing Stations -->
            <h2>Your Stations</h2>
            <div class="stations-grid">
                <?php foreach ($stations as $station): ?>
                    <div class="station-card">
                        <div class="station-header">
                            <h3 class="station-name"><?php echo htmlspecialchars($station['name']); ?></h3>
                            <span class="<?php echo $station['is_active'] ? 'alert alert-success' : 'alert alert-error'; ?>" 
                                  style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                                <?php echo $station['is_active'] ? 'ACTIVE' : 'INACTIVE'; ?>
                            </span>
                        </div>
                        
                        <p style="color: #666; margin-bottom: 1rem;"><?php echo htmlspecialchars($station['address']); ?></p>
                        
                        <div class="fuel-prices">
                            <div class="fuel-price">
                                <div class="fuel-type">Petrol</div>
                                <div class="price">₹<?php echo $station['petrol_price'] ?: '0.00'; ?></div>
                            </div>
                            <div class="fuel-price">
                                <div class="fuel-type">Diesel</div>
                                <div class="price">₹<?php echo $station['diesel_price'] ?: '0.00'; ?></div>
                            </div>
                        </div>
                        
                        <!-- Update Prices Form -->
                        <form method="POST" style="margin-top: 1rem;">
                            <input type="hidden" name="action" value="update_price">
                            <input type="hidden" name="station_id" value="<?php echo $station['id']; ?>">
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; align-items: end;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="fuel_type_<?php echo $station['id']; ?>" style="font-size: 0.8rem;">Fuel</label>
                                    <select id="fuel_type_<?php echo $station['id']; ?>" name="fuel_type" class="form-control" required>
                                        <option value="petrol">Petrol</option>
                                        <option value="diesel">Diesel</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="price_<?php echo $station['id']; ?>" style="font-size: 0.8rem;">Price</label>
                                    <input type="number" id="price_<?php echo $station['id']; ?>" name="price" 
                                           class="form-control" step="0.01" min="0" required>
                                </div>
                                <button type="submit" class="btn btn-primary" style="height: fit-content;">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($stations)): ?>
                    <div class="station-card" style="text-align: center; grid-column: 1 / -1;">
                        <i class="fas fa-gas-pump" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <h3>No stations yet</h3>
                        <p>Add your first fuel station using the form above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="assets/js/alerts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($message): ?>
            showToast('<?php echo addslashes($message); ?>', 'success', 'Success!');
            <?php endif; ?>
            
            <?php if ($error): ?>
            showToast('<?php echo addslashes($error); ?>', 'error', 'Error');
            <?php endif; ?>
            
            // Enhanced form validation for price updates
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const priceInput = this.querySelector('input[name="price"]');
                    if (priceInput) {
                        const price = parseFloat(priceInput.value);
                        if (price <= 0) {
                            e.preventDefault();
                            showToast('Price must be greater than zero', 'warning', 'Invalid Price');
                            return;
                        }
                        if (price > 200) {
                            e.preventDefault();
                            showConfirm({
                                title: 'High Price Alert',
                                message: `The price ₹${price} seems unusually high. Are you sure this is correct?`,
                                confirmText: 'Yes, Update',
                                cancelText: 'Let me check',
                                onConfirm: () => {
                                    this.submit();
                                }
                            });
                            return;
                        }
                    }
                });
            });
            
            // Station management tips
            setTimeout(() => {
                const stationCount = <?php echo count($stations); ?>;
                if (stationCount === 0) {
                    showToast('Add your first fuel station to start managing prices and attracting customers!', 'info', 'Getting Started', 6000);
                } else if (stationCount === 1) {
                    showToast('Great! You can add multiple stations and manage them all from this dashboard.', 'info', 'Tip', 5000);
                }
            }, 2000);
        });
    </script>
</body>
</html>