# Inventory Control System — VS Code + XAMPP

## Requirements
- XAMPP (Apache + MySQL)
- VS Code
- PHP 8+ recommended

## Step 1 — Copy the project
Copy this folder to:
C:\xampp\htdocs\inventory-control-system

## Step 2 — Start XAMPP
Open XAMPP Control Panel and start:
- Apache
- MySQL

## Step 3 — Create the database
Open:
http://localhost/phpmyadmin

Click Import and select:
database/inventory.sql

Or open the SQL file, copy all SQL, paste it into phpMyAdmin > SQL, and run it.

## Step 4 — Check database settings
Open:
includes/database.php

Default XAMPP settings:
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

If your MySQL password is different, change $pass.

## Step 5 — Open the project
Visit:
http://localhost/inventory-control-system/

## Demo login
Username: admin
Password: admin123

## Main pages
Dashboard:
http://localhost/inventory-control-system/dashboard.php

Products:
http://localhost/inventory-control-system/products.php

Categories:
http://localhost/inventory-control-system/categories.php

Suppliers:
http://localhost/inventory-control-system/suppliers.php

Purchases:
http://localhost/inventory-control-system/purchases.php

Sales / Stock Out:
http://localhost/inventory-control-system/sales.php

Stock:
http://localhost/inventory-control-system/stock.php

Reports:
http://localhost/inventory-control-system/reports.php

## Important
This is a college-project starter. Before using it for a real business, add CSRF protection, stronger validation, role-based permissions, audit logs, and proper production database credentials.
