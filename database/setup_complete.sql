-- Complete Database Setup Script for Petromine
-- Run this file to set up the entire database with all sample data

-- Create and use database
CREATE DATABASE IF NOT EXISTS petromine;
USE petromine;

-- Source all the SQL files in order
SOURCE schema.sql;
SOURCE sample_data.sql;
SOURCE demo_users.sql;
SOURCE price_history.sql;

-- Verify the setup
SELECT 'Database Setup Complete!' as Status;

-- Show summary statistics
SELECT 
    'Users' as Table_Name,
    COUNT(*) as Record_Count,
    GROUP_CONCAT(DISTINCT role) as Roles
FROM users
UNION ALL
SELECT 
    'Fuel Stations' as Table_Name,
    COUNT(*) as Record_Count,
    CONCAT(SUM(is_active), ' Active') as Roles
FROM fuel_stations
UNION ALL
SELECT 
    'Fuel Prices' as Table_Name,
    COUNT(*) as Record_Count,
    GROUP_CONCAT(DISTINCT fuel_type) as Roles
FROM fuel_prices
UNION ALL
SELECT 
    'Station Services' as Table_Name,
    COUNT(*) as Record_Count,
    CONCAT(SUM(is_available), ' Available') as Roles
FROM station_services
UNION ALL
SELECT 
    'Save to Buy' as Table_Name,
    COUNT(*) as Record_Count,
    GROUP_CONCAT(DISTINCT status) as Roles
FROM save_to_buy;

-- Show demo login credentials
SELECT 
    'Demo Login Credentials' as Info,
    '' as Username,
    '' as Email,
    '' as Password,
    '' as Role
UNION ALL
SELECT 
    '=====================' as Info,
    '============' as Username,
    '===============' as Email,
    '============' as Password,
    '============' as Role
UNION ALL
SELECT 
    'Admin Access' as Info,
    'admin' as Username,
    'admin@petromine.com' as Email,
    'password' as Password,
    'admin' as Role
UNION ALL
SELECT 
    'Demo Customer' as Info,
    'demo_customer' as Username,
    'customer@demo.com' as Email,
    'password123' as Password,
    'customer' as Role
UNION ALL
SELECT 
    'Demo Owner' as Info,
    'demo_owner' as Username,
    'owner@demo.com' as Email,
    'password123' as Password,
    'pump_owner' as Role
UNION ALL
SELECT 
    'Demo Admin' as Info,
    'demo_admin' as Username,
    'admin@demo.com' as Email,
    'password123' as Password,
    'admin' as Role;