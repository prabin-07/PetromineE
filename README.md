# Petromine - Real-time Fuel Price Platform

Petromine is an innovative web application that provides real-time updates on petrol and diesel prices across various fuel stations in India. The platform features a unique "Save to Buy" system that allows users to lock in current fuel prices for future purchases. All prices are displayed in Indian Rupees (₹).

## Features

### For Customers
- **Real-time Price Monitoring**: View current petrol and diesel prices across multiple stations
- **Save to Buy**: Lock in current fuel prices for up to 7 days
- **Station Services**: View additional services available at each station (oil change, nitrogen fill, etc.)
- **Dashboard**: Track savings, active price locks, and purchase history
- **Price Comparison**: Compare prices across different stations

### For Petrol Pump Owners
- **Station Management**: Add and manage multiple fuel stations
- **Price Updates**: Update fuel prices in real-time
- **Service Management**: Manage available services and pricing
- **Sales Tracking**: Monitor customer purchases and locked prices

### For System Administrators
- **User Management**: Oversee all platform users
- **Station Oversight**: Monitor all registered fuel stations
- **Transaction Monitoring**: Track all platform transactions
- **Data Analytics**: View platform usage statistics

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with responsive design
- **Icons**: Font Awesome 6.0

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Quick Setup (Recommended)

1. **Clone/Download the project**
   ```bash
   git clone <repository-url>
   cd petromine
   ```

2. **Run the automatic installer**
   - Open your browser and navigate to: `http://your-domain/install.php`
   - The installer will automatically:
     - Create the database
     - Set up all tables
     - Insert comprehensive sample data
     - Create demo users

3. **Start using the application**
   - Access the homepage: `http://your-domain/index.php`
   - Login with demo credentials (see below)

### Manual Setup (Alternative)

1. **Database Setup**
   ```bash
   mysql -u root -p
   CREATE DATABASE petromine;
   USE petromine;
   SOURCE database/setup_complete.sql;
   ```

2. **Configure Database Connection**
   - Edit `config/database.php` if your credentials differ from defaults

### Demo Login Credentials

| Role | Email | Password | Description |
|------|-------|----------|-------------|
| **Admin** | admin@petromine.com | password | Original admin account |
| **Demo Admin** | admin@demo.com | password123 | Demo admin with full access |
| **Customer** | customer@demo.com | password123 | Demo customer with sample purchases |
| **Pump Owner** | owner@demo.com | password123 | Demo station owner |
| **Customer 2** | alice@demo.com | password123 | Additional customer account |
| **Customer 3** | bob@demo.com | password123 | Another customer with transactions |

## Project Structure

```
petromine/
├── api/                      # API endpoints
│   ├── stations.php         # Station data API
│   ├── save-to-buy.php      # Save to buy functionality
│   ├── user-stats.php       # User statistics
│   ├── saved-purchases.php  # User purchase history
│   └── redeem-purchase.php  # Purchase redemption
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   └── js/
│       └── main.js          # JavaScript functionality
├── config/
│   └── database.php         # Database configuration
├── database/
│   ├── schema.sql           # Database schema
│   ├── sample_data.sql      # Comprehensive sample data
│   ├── demo_users.sql       # Demo user accounts
│   ├── price_history.sql    # Historical price data
│   └── setup_complete.sql   # Complete setup script
├── index.php                # Homepage
├── login.php                # User login
├── register.php             # User registration
├── dashboard.php            # User dashboard
├── save-to-buy.php          # Save to buy interface
├── manage-station.php       # Station management (owners)
├── logout.php               # User logout
├── install.php              # Automatic installation script
└── README.md                # This file
```

## Sample Data Included

The application comes with comprehensive sample data:

### 🏪 **13 Fuel Stations** across different locations:
- Shell stations (Downtown, Uptown, Mall Road, 24/7)
- BP stations (Highway, Riverside, Airport)
- Exxon stations (City Center, Westside)
- Chevron stations (Plaza, Highway Junction)
- Texaco stations (City Center, Express Lane)

### 👥 **Multiple User Accounts**:
- **7 Demo Users** with easy-to-remember credentials
- **5 Pump Owners** managing different station chains
- **1 System Admin** with full platform access
- **Sample transactions** showing the Save-to-Buy feature in action

### 💰 **Realistic Price Data** (in Indian Rupees):
- **Current prices** for petrol (₹101-₹104/L) and diesel (₹88-₹91/L) at all stations
- **30 days of price history** showing realistic fluctuations
- **Weekend and holiday price variations** (higher during festivals)
- **Recent price updates** demonstrating real-time changes

### 🔧 **Station Services** (60+ services):
- Engine oil changes (₹1,999 - ₹2,999) with different pricing tiers
- Nitrogen air filling services (₹45 - ₹55)
- Car wash and detailing options (₹150 - ₹7,999)
- Convenience store amenities
- 24/7 services and emergency support
- Location-specific services (airport shuttle, riverside picnic area, etc.)

### 📊 **Transaction History**:
- **Active Save-to-Buy purchases** with different expiry dates
- **Redeemed purchases** showing customer savings
- **Expired purchases** demonstrating the time-limited nature
- **Price lock scenarios** with realistic quantities and amounts

## Database Schema

### Users Table
- Stores user information with role-based access (customer, pump_owner, admin)
- Includes demo accounts with hashed passwords

### Fuel Stations Table
- Contains station information including location and owner details
- GPS coordinates for mapping functionality

### Fuel Prices Table
- Tracks historical and current fuel prices for each station
- Includes 30 days of price history for trend analysis

### Station Services Table
- Manages additional services offered by each station
- Pricing and availability information

### Save to Buy Table
- Handles price locking functionality with expiration dates
- Tracks purchase status (active, redeemed, expired)

## Key Features Implementation

### Save to Buy System
- Users can lock current fuel prices for up to 7 days
- Automatic expiration handling
- Redemption tracking and validation

### Real-time Price Updates
- Station owners can update prices instantly
- Customers see current prices across all stations
- Price history tracking for analytics

### Role-based Access Control
- Three user roles with different permissions
- Secure session management
- Protected API endpoints

## API Endpoints

- `GET /api/stations.php` - Retrieve all active stations with current prices
- `POST /api/save-to-buy.php` - Lock fuel prices for future purchase
- `GET /api/user-stats.php` - Get user statistics and savings
- `GET /api/saved-purchases.php` - Retrieve user's saved purchases
- `POST /api/redeem-purchase.php` - Redeem a saved purchase

