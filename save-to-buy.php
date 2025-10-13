<?php
require_once 'includes/session.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Require login to access save-to-buy feature
requireLogin();

// Check if user is admin - admins cannot buy fuel
if (hasRole('admin')) {
    header('Location: dashboard.php?error=admin_no_fuel_purchase');
    exit();
}

$station_id = isset($_GET['station_id']) ? (int)$_GET['station_id'] : 0;

if (!$station_id) {
    header('Location: index.php');
    exit();
}

// Get station details
$query = "
    SELECT 
        fs.id,
        fs.name,
        fs.address,
        MAX(CASE WHEN fp.fuel_type = 'petrol' THEN fp.price END) as petrol_price,
        MAX(CASE WHEN fp.fuel_type = 'diesel' THEN fp.price END) as diesel_price
    FROM fuel_stations fs
    LEFT JOIN fuel_prices fp ON fs.id = fp.station_id
    WHERE fs.id = :station_id AND fs.is_active = 1
    GROUP BY fs.id, fs.name, fs.address
";

$stmt = $db->prepare($query);
$stmt->bindParam(':station_id', $station_id);
$stmt->execute();
$station = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$station) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save to Buy - Petromine</title>
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container" style="max-width: 600px; margin: 2rem auto;">
            <div class="station-card">
                <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">
                    <i class="fas fa-lock"></i> Save to Buy
                </h2>
                
                <div class="station-header">
                    <h3 class="station-name"><?php echo htmlspecialchars($station['name']); ?></h3>
                </div>
                
                <p style="color: #666; margin-bottom: 2rem;"><?php echo htmlspecialchars($station['address']); ?></p>
                
                <div class="fuel-prices" style="margin-bottom: 2rem;">
                    <div class="fuel-price">
                        <div class="fuel-type">Petrol</div>
                        <div class="price">₹<?php echo $station['petrol_price']; ?></div>
                    </div>
                    <div class="fuel-price">
                        <div class="fuel-type">Diesel</div>
                        <div class="price">₹<?php echo $station['diesel_price']; ?></div>
                    </div>
                </div>
                
                <form id="saveToBuyForm">
                    <input type="hidden" name="station_id" value="<?php echo $station_id; ?>">
                    
                    <div class="form-group">
                        <label for="fuel_type">Fuel Type</label>
                        <select id="fuel_type" name="fuel_type" class="form-control" required>
                            <option value="">Select Fuel Type</option>
                            <option value="petrol" data-price="<?php echo $station['petrol_price']; ?>">Petrol - ₹<?php echo $station['petrol_price']; ?>/L</option>
                            <option value="diesel" data-price="<?php echo $station['diesel_price']; ?>">Diesel - ₹<?php echo $station['diesel_price']; ?>/L</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity (Liters)</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" 
                               min="1" max="100" step="0.1" required>
                    </div>
                    
                    <input type="hidden" id="price" name="price" value="">
                    
                    <div class="form-group">
                        <label>Total Amount</label>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">
                            ₹<span id="total">0.00</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-success" style="margin: 1rem 0;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Lock Period:</strong> 7 days from purchase<br>
                        <strong>Note:</strong> You can redeem this purchase at the selected station within the lock period.
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-lock"></i> Lock Price & Save
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="assets/js/alerts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fuelTypeSelect = document.getElementById('fuel_type');
            const quantityInput = document.getElementById('quantity');
            const priceInput = document.getElementById('price');
            const totalSpan = document.getElementById('total');
            
            function updatePrice() {
                const selectedOption = fuelTypeSelect.options[fuelTypeSelect.selectedIndex];
                if (selectedOption.value) {
                    const price = selectedOption.getAttribute('data-price');
                    priceInput.value = price;
                    updateTotal();
                    
                    // Show price lock info
                    const fuelType = selectedOption.value;
                    showToast(`Current ${fuelType} price: ₹${price}/L - Lock this price for 7 days!`, 'info', 'Price Lock Available', 4000);
                }
            }
            
            function updateTotal() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const total = (quantity * price).toFixed(2);
                totalSpan.textContent = total;
                
                // Show savings estimate
                if (quantity > 0 && price > 0) {
                    const potentialSavings = (quantity * 2).toFixed(2); // Estimate ₹2 savings per liter
                    if (quantity >= 10) {
                        showToast(`Potential savings: ₹${potentialSavings} if prices increase!`, 'success', 'Smart Choice!', 3000);
                    }
                }
            }
            
            fuelTypeSelect.addEventListener('change', updatePrice);
            quantityInput.addEventListener('input', updateTotal);
            
            // Enhanced quantity validation
            quantityInput.addEventListener('blur', function() {
                const quantity = parseFloat(this.value);
                if (quantity > 0 && quantity < 5) {
                    showToast('Consider buying at least 5 liters for better savings!', 'warning', 'Tip', 3000);
                } else if (quantity > 50) {
                    showToast('Large quantity selected. Make sure you have adequate storage!', 'warning', 'Notice', 4000);
                }
            });
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>