document.addEventListener('DOMContentLoaded', function() {
    loadStations();
});

async function loadStations() {
    try {
        const response = await fetch('api/stations.php');
        const stations = await response.json();
        
        const stationsGrid = document.getElementById('stationsGrid');
        if (!stationsGrid) return;
        
        stationsGrid.innerHTML = '';
        
        stations.forEach(station => {
            const stationCard = createStationCard(station);
            stationsGrid.appendChild(stationCard);
        });
    } catch (error) {
        console.error('Error loading stations:', error);
    }
}

function createStationCard(station) {
    const card = document.createElement('div');
    card.className = 'station-card';
    
    const servicesHtml = station.services.map(service => 
        `<span class="service-tag">${service.service_name}</span>`
    ).join('');
    
    card.innerHTML = `
        <div class="station-header">
            <h3 class="station-name">${station.name}</h3>
            <span class="station-distance">2.3 km</span>
        </div>
        <div class="fuel-prices">
            <div class="fuel-price">
                <div class="fuel-type">Petrol</div>
                <div class="price">₹${station.petrol_price || 'N/A'}</div>
            </div>
            <div class="fuel-price">
                <div class="fuel-type">Diesel</div>
                <div class="price">₹${station.diesel_price || 'N/A'}</div>
            </div>
        </div>
        <div class="services">
            <h4>Available Services</h4>
            <div class="service-tags">
                ${servicesHtml}
            </div>
        </div>
        <div class="station-actions">
            ${window.userRole !== 'admin' ? `
                <button class="btn btn-save" onclick="openSaveToBuy(${station.id})">
                    <i class="fas fa-lock"></i> Save to Buy
                </button>
            ` : ''}
            <button class="btn btn-info" onclick="viewStationDetails(${station.id})">
                <i class="fas fa-info-circle"></i> Details
            </button>
        </div>
    `;
    
    return card;
}

function openSaveToBuy(stationId) {
    if (!isLoggedIn()) {
        showConfirm({
            title: 'Login Required',
            message: 'You need to login to use the Save to Buy feature. Would you like to go to the login page?',
            confirmText: 'Login',
            cancelText: 'Cancel',
            type: 'info',
            onConfirm: () => {
                window.location.href = 'login.php';
            }
        });
        return;
    }
    
    // Check if user is admin - admins cannot buy fuel
    if (window.userRole === 'admin') {
        showToast('Admin users are not allowed to purchase fuel. You can only manage the platform from the admin panel.', 'error', 'Access Restricted', 5000);
        return;
    }
    
    window.location.href = `save-to-buy.php?station_id=${stationId}`;
}

function viewStationDetails(stationId) {
    window.location.href = `station-details.php?id=${stationId}`;
}

function isLoggedIn() {
    // This would be set by PHP when rendering the page
    return window.userLoggedIn || false;
}

// Save to Buy functionality
function initSaveToBuy() {
    const form = document.getElementById('saveToBuyForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api/save-to-buy.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Fuel price locked successfully! Redirecting to dashboard...', 'success', 'Success!');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 2000);
            } else {
                showToast(result.message, 'error', 'Error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error', 'Network Error');
        }
    });
    
    // Update total when quantity changes
    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('price');
    const totalSpan = document.getElementById('total');
    
    if (quantityInput && priceInput && totalSpan) {
        quantityInput.addEventListener('input', updateTotal);
        
        function updateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = (quantity * price).toFixed(2);
            totalSpan.textContent = total;
        }
    }
}

// Initialize Save to Buy form if present
document.addEventListener('DOMContentLoaded', initSaveToBuy);

// Dashboard functionality
function loadDashboardData() {
    loadUserStats();
    loadSavedPurchases();
}

async function loadUserStats() {
    try {
        const response = await fetch('api/user-stats.php');
        const stats = await response.json();
        
        document.getElementById('totalSavings').textContent = '₹' + stats.total_savings;
        document.getElementById('activeLocks').textContent = stats.active_locks;
        document.getElementById('totalPurchases').textContent = stats.total_purchases;
    } catch (error) {
        console.error('Error loading user stats:', error);
    }
}

async function loadSavedPurchases() {
    try {
        const response = await fetch('api/saved-purchases.php');
        const purchases = await response.json();
        
        const container = document.getElementById('savedPurchases');
        if (!container) return;
        
        container.innerHTML = '';
        
        purchases.forEach(purchase => {
            const purchaseCard = createPurchaseCard(purchase);
            container.appendChild(purchaseCard);
        });
    } catch (error) {
        console.error('Error loading saved purchases:', error);
    }
}

function createPurchaseCard(purchase) {
    const card = document.createElement('div');
    card.className = 'station-card';
    
    const statusClass = purchase.status === 'active' ? 'success' : 
                       purchase.status === 'expired' ? 'error' : 'info';
    
    card.innerHTML = `
        <div class="station-header">
            <h3 class="station-name">${purchase.station_name}</h3>
            <span class="alert alert-${statusClass}" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                ${purchase.status.toUpperCase()}
            </span>
        </div>
        <div class="fuel-prices">
            <div class="fuel-price">
                <div class="fuel-type">${purchase.fuel_type}</div>
                <div class="price">₹${purchase.locked_price}</div>
            </div>
            <div class="fuel-price">
                <div class="fuel-type">Quantity</div>
                <div class="price">${purchase.quantity}L</div>
            </div>
        </div>
        <p><strong>Total:</strong> ₹${purchase.total_amount}</p>
        <p><strong>Expires:</strong> ${new Date(purchase.expiry_date).toLocaleDateString()}</p>
        ${purchase.status === 'active' ? 
            `<button class="btn btn-primary" onclick="redeemPurchase(${purchase.id})">
                <i class="fas fa-check"></i> Redeem Now
            </button>` : ''}
    `;
    
    return card;
}

async function redeemPurchase(purchaseId) {
    showConfirm({
        title: 'Redeem Purchase',
        message: 'Are you sure you want to redeem this purchase? This action cannot be undone.',
        confirmText: 'Redeem Now',
        cancelText: 'Cancel',
        type: 'warning',
        onConfirm: async () => {
            const loadingModal = showLoading('Redeeming purchase...');
            
            try {
                const response = await fetch('api/redeem-purchase.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ purchase_id: purchaseId })
                });
                
                const result = await response.json();
                closeModal(loadingModal);
                
                if (result.success) {
                    showToast('Purchase redeemed successfully!', 'success', 'Success!');
                    loadSavedPurchases();
                    loadUserStats();
                } else {
                    showToast(result.message, 'error', 'Error');
                }
            } catch (error) {
                closeModal(loadingModal);
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error', 'Network Error');
            }
        }
    });
}

// Initialize dashboard if on dashboard page
if (window.location.pathname.includes('dashboard.php')) {
    document.addEventListener('DOMContentLoaded', loadDashboardData);
}