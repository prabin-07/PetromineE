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

// Payment Methods: Google Pay (UPI), Card, Cash
const PAYMENT_METHODS = [
    { value: 'gpay', label: 'Google Pay (UPI)', icon: 'fab fa-google-pay', color: '#4285F4' },
    { value: 'card', label: 'Credit / Debit Cards', icon: 'fas fa-credit-card', color: '#FF6B6B' },
    { value: 'cash', label: 'Pay with Cash (COD)', icon: 'fas fa-money-bill-wave', color: '#51CF66' }
];

let cashfreeInstance = null;

async function redeemPurchase(purchaseId) {
    // Get purchase details first to show amount
    try {
        const purchases = await loadSavedPurchasesData();
        const purchase = purchases.find(p => p.id === purchaseId);
        
        if (!purchase) {
            showToast('Purchase not found.', 'error', 'Error');
            return;
        }

        const amount = parseFloat(purchase.total_amount) || 0;

        // Show payment method selection using SweetAlert2
        const { value: paymentMethod } = await Swal.fire({
            title: 'Choose Payment Method',
            html: `
                <div style="text-align: left; margin: 1.5rem 0;">
                    <p style="margin-bottom: 1.5rem; color: #232946; font-size: 1.1rem;">
                        Total Amount: <strong style="color: #27AE60;">₹${amount.toFixed(2)}</strong>
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        ${PAYMENT_METHODS.map(method => `
                            <label style="
                                display: flex;
                                align-items: center;
                                gap: 1rem;
                                padding: 1rem;
                                border: 2px solid #e0e0e0;
                                border-radius: 12px;
                                cursor: pointer;
                                transition: all 0.3s;
                                background: white;
                            " class="payment-method-option" data-value="${method.value}">
                                <input type="radio" name="paymentMethod" value="${method.value}" 
                                    style="width: 20px; height: 20px; cursor: pointer;">
                                <i class="${method.icon}" style="font-size: 1.5rem; color: ${method.color};"></i>
                                <span style="font-weight: 600; color: #232946; flex: 1;">${method.label}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Proceed',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#27AE60',
            cancelButtonColor: '#dc3545',
            width: '500px',
            didOpen: () => {
                const options = document.querySelectorAll('.payment-method-option');
                options.forEach(opt => {
                    opt.addEventListener('click', function() {
                        options.forEach(o => o.style.borderColor = '#e0e0e0');
                        this.style.borderColor = '#27AE60';
                        this.style.boxShadow = '0 0 0 3px rgba(39, 174, 96, 0.1)';
                        this.querySelector('input[type="radio"]').checked = true;
                    });
                    opt.addEventListener('mouseenter', function() {
                        if (!this.querySelector('input[type="radio"]').checked) {
                            this.style.borderColor = '#b8c1ec';
                        }
                    });
                    opt.addEventListener('mouseleave', function() {
                        if (!this.querySelector('input[type="radio"]').checked) {
                            this.style.borderColor = '#e0e0e0';
                        }
                    });
                });
                // Select first option by default
                if (options[0]) {
                    options[0].querySelector('input[type="radio"]').checked = true;
                    options[0].style.borderColor = '#27AE60';
                    options[0].style.boxShadow = '0 0 0 3px rgba(39, 174, 96, 0.1)';
                }
            },
            preConfirm: () => {
                const selected = document.querySelector('input[name="paymentMethod"]:checked');
                return selected ? selected.value : null;
            }
        });

        if (paymentMethod) {
            initiateRedeemPayment(purchaseId, paymentMethod);
        }
    } catch (error) {
        console.error('Error loading purchase:', error);
        showToast('Unable to load purchase details.', 'error', 'Error');
    }
}

async function loadSavedPurchasesData() {
    try {
        const response = await fetch('api/saved-purchases.php');
        return await response.json();
    } catch (error) {
        console.error('Error loading purchases:', error);
        return [];
    }
}

async function initiateRedeemPayment(purchaseId, paymentMethod) {
    const loadingSwal = Swal.fire({
        title: 'Processing...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch('api/create_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                purchase_id: purchaseId, 
                payment_method: paymentMethod 
            })
        });

        const result = await response.json();
        await Swal.close();

        if (!result.success) {
            await Swal.fire({
                icon: 'error',
                title: 'Payment Error',
                text: result.message || 'Unable to start payment.',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Handle different payment methods
        if (paymentMethod === 'cash') {
            // Cash payment: Instant success
            await Swal.fire({
                icon: 'success',
                title: 'Payment Confirmed!',
                html: `
                    <p>Your cash payment has been confirmed.</p>
                    <p style="margin-top: 1rem;"><strong>Order ID:</strong> ${result.order_id}</p>
                    <p style="margin-top: 0.5rem;">Please collect your fuel at the station.</p>
                `,
                confirmButtonColor: '#27AE60'
            });
            loadSavedPurchases();
            loadUserStats();
            return;
        }

        if (paymentMethod === 'gpay') {
            // Google Pay UPI Intent
            await Swal.fire({
                icon: 'info',
                title: 'Opening Google Pay',
                html: `
                    <p>You will be redirected to Google Pay to complete the payment.</p>
                    <p style="margin-top: 1rem;"><strong>Amount:</strong> ₹${result.amount}</p>
                    <p style="margin-top: 0.5rem;"><strong>Order ID:</strong> ${result.order_id}</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Open Google Pay',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4285F4',
                cancelButtonColor: '#dc3545'
            }).then(async (swalResult) => {
                if (swalResult.isConfirmed) {
                    // Open UPI Intent link
                    window.location.href = result.upi_link;
                    
                    // Show manual confirmation dialog after a delay
                    setTimeout(async () => {
                        const { value: confirmPaid } = await Swal.fire({
                            icon: 'question',
                            title: 'Payment Completed?',
                            html: `
                                <p>Have you completed the payment in Google Pay?</p>
                                <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                                    If yes, your order will be processed. If not, you can try again.
                                </p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Yes, I Paid',
                            cancelButtonText: 'Not Yet',
                            confirmButtonColor: '#27AE60',
                            cancelButtonColor: '#dc3545'
                        });

                        if (confirmPaid) {
                            // Manually mark as paid (you may want to verify via API)
                            await handleGPayConfirmation(result.order_id, purchaseId);
                        }
                    }, 3000);
                }
            });
            return;
        }

        if (paymentMethod === 'card') {
            // Card payment via Cashfree
            const sessionId = result.payment_session_id;
            const orderId = result.order_id;
            
            const checkoutStarted = await openCashfreeCheckout(sessionId, result.payment_link);

            if (checkoutStarted) {
                await Swal.fire({
                    icon: 'info',
                    title: 'Payment Window Opened',
                    text: 'Complete the payment in the checkout window. We will verify automatically.',
                    confirmButtonColor: '#27AE60',
                    timer: 3000,
                    timerProgressBar: true
                });
                pollPaymentVerification(orderId, purchaseId);
            }
            return;
        }

    } catch (error) {
        await Swal.close();
        console.error('Payment init error:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Payment Error',
            text: 'Something went wrong while starting the payment. Please try again.',
            confirmButtonColor: '#dc3545'
        });
    }
}

async function handleGPayConfirmation(orderId, purchaseId) {
    // For Google Pay, we rely on user confirmation
    // In production, you might want to implement a manual verification endpoint
    const { value: confirmed } = await Swal.fire({
        icon: 'warning',
        title: 'Manual Verification',
        html: `
            <p>For Google Pay payments, please contact support with your Order ID to verify the payment.</p>
            <p style="margin-top: 1rem;"><strong>Order ID:</strong> ${orderId}</p>
        `,
        showCancelButton: true,
        confirmButtonText: 'I Understand',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#27AE60'
    });

    if (confirmed) {
        loadSavedPurchases();
        loadUserStats();
    }
}

async function openCashfreeCheckout(paymentSessionId, fallbackLink = null) {
    if (paymentSessionId) {
        const instance = ensureCashfreeInstance();
        if (instance && typeof instance.checkout === 'function') {
            try {
                await instance.checkout({
                    paymentSessionId,
                    redirectTarget: '_modal'
                });
                return true;
            } catch (error) {
                console.warn('Cashfree checkout closed:', error);
                showToast('Payment window closed before completion. You can retry.', 'warning', 'Payment Pending');
                return false;
            }
        }
    }

    if (fallbackLink) {
        window.open(fallbackLink, '_blank', 'noopener');
        return true;
    }

    showToast('Unable to open the payment gateway. Please check your network and retry.', 'error', 'Gateway Error');
    return false;
}

function ensureCashfreeInstance() {
    if (cashfreeInstance) {
        return cashfreeInstance;
    }
    if (typeof Cashfree === 'undefined') {
        return null;
    }
    const dataset = document.body && document.body.dataset ? document.body.dataset : {};
    const configMode = (dataset.cashfreeMode || window.cashfreeMode || 'sandbox').toLowerCase();
    const mode = configMode === 'production' ? 'production' : 'sandbox';
    cashfreeInstance = Cashfree({ mode });
    return cashfreeInstance;
}

function pollPaymentVerification(orderId, purchaseId, attempt = 0) {
    setTimeout(async () => {
        try {
            const result = await requestPaymentVerification(orderId, purchaseId);
            if (result.success && result.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: 'Payment Successful!',
                    text: 'Your payment has been verified. Your purchase is being processed.',
                    confirmButtonColor: '#27AE60'
                });
                loadSavedPurchases();
                loadUserStats();
            } else if (result.status === 'pending' && attempt < 5) {
                pollPaymentVerification(orderId, purchaseId, attempt + 1);
            } else if (result.status === 'failed') {
                await Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: result.message || 'Payment failed. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            } else if (attempt >= 5) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Verification Timeout',
                    text: 'Payment verification is taking longer than expected. Please refresh and check your order status.',
                    confirmButtonColor: '#ffc107'
                });
            }
        } catch (error) {
            console.error('Verification error:', error);
            if (attempt < 5) {
                pollPaymentVerification(orderId, purchaseId, attempt + 1);
            } else {
                await Swal.fire({
                    icon: 'error',
                    title: 'Verification Error',
                    text: 'Unable to verify payment right now. Please refresh and try again.',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    }, attempt === 0 ? 4000 : 6000);
}

async function requestPaymentVerification(orderId, purchaseId) {
    const response = await fetch('api/verify_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId, purchase_id: purchaseId })
    });
    return response.json();
}

function initRedeemPaymentHelpers() {
    ensureCashfreeInstance();
}

// Initialize dashboard if on dashboard page
if (window.location.pathname.includes('dashboard.php')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardData();
        initRedeemPaymentHelpers();
    });
}