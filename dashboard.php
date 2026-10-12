<?php
// dashboard.php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Total Products Count
$total_products = 0;
$prod_q = $conn->query("SELECT COUNT(*) AS count FROM products");
if ($prod_q && $row = $prod_q->fetch_assoc()) {
    $total_products = $row['count'] ?? 0;
}

// 2. Total Sales Revenue
$total_sales = 0.00;
$sales_q = $conn->query("SELECT SUM(total) AS revenue FROM sales");
if ($sales_q && $row = $sales_q->fetch_assoc()) {
    $total_sales = $row['revenue'] ?? 0.00;
}

// 3. Low Stock Count
$low_stock_count = 0;
$stock_q = $conn->query("SELECT COUNT(*) AS count FROM products WHERE quantity <= min_stock_level");
if ($stock_q && $row = $stock_q->fetch_assoc()) {
    $low_stock_count = $row['count'] ?? 0;
}

// 4. Fetch Recent Client Orders
$orders_query = "SELECT o.id, o.created_at, o.total_amount, o.status, c.name as client_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                ORDER BY o.id DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);
// 5. Fetch Low Stock Items
$low_stock_items = $conn->query("SELECT * FROM products WHERE quantity <= min_stock_level LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory Control System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; display: flex; min-height: 100vh; }
        
        /* Sidebar Styling with Icons */
        .sidebar { width: 260px; background-color: #0f172a; color: #94a3b8; display: flex; flex-direction: column; justify-content: space-between; padding: 24px 16px; position: fixed; height: 100vh; }
        .sidebar-brand { font-size: 18px; font-weight: 700; color: #ffffff; padding: 0 12px 24px 12px; border-bottom: 1px solid #1e293b; display: flex; align-items: center; gap: 12px; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 16px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: #2563eb; color: #ffffff; }
        
        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header-title h1 { font-size: 26px; font-weight: 700; color: #0f172a; }
        .header-title p { font-size: 14px; color: #64748b; margin-top: 4px; }
        
        .user-profile { display: flex; align-items: center; gap: 12px; background: #ffffff; padding: 8px 16px; border-radius: 30px; border: 1px solid #e2e8f0; }
        .user-avatar { width: 36px; height: 36px; background-color: #2563eb; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }

        /* Metrics Cards */
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
        .metric-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .metric-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .icon-blue { background-color: #eff6ff; color: #2563eb; }
        .icon-green { background-color: #f0fdf4; color: #16a34a; }
        .icon-amber { background-color: #fffbeb; color: #d97706; }
        .icon-red { background-color: #fef2f2; color: #dc2626; }
        
        .metric-label { font-size: 13px; color: #64748b; font-weight: 500; }
        .metric-value { font-size: 20px; font-weight: 700; color: #0f172a; margin: 2px 0; }
        .metric-sub { font-size: 12px; color: #94a3b8; }

        /* Dashboard Grid Sections */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 600; color: #0f172a; }
        .card-subtitle { font-size: 13px; color: #64748b; margin-top: 2px; }
        .card-link { font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500; }
        .card-link:hover { text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 10px 12px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        
        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-icon { font-size: 32px; margin-bottom: 8px; }
        .empty-text { font-size: 15px; font-weight: 600; color: #1e293b; }
        .empty-sub { font-size: 13px; color: #64748b; margin-top: 4px; margin-bottom: 16px; }
        
        .btn-add { background-color: #2563eb; color: #ffffff; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 13px; display: inline-block; }
        .btn-add:hover { background-color: #1d4ed8; }

        /* Quick Actions */
        .quick-actions-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation with Icons -->
    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div style="width: 32px; height: 32px; background-color: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 16px;">📦</div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #ffffff; line-height: 1.2;">Inventory</div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 400;">Control System</div>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="products.php">📦 Products</a></li>
                <li><a href="categories.php">📁 Categories</a></li>
                <li><a href="suppliers.php">🏢 Suppliers</a></li>
                <li><a href="purchases.php">🛒 Purchases</a></li>
                <li><a href="sales.php">💰 Sales</a></li>
                <li><a href="stock.php">📋 Stock</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
            </ul>
        </div>
        <div>
            <ul class="sidebar-menu" style="margin-bottom: 12px;">
                <li><a href="settings.php">⚙️ Settings</a></li>
                <li><a href="logout.php" style="color: #ef4444;">🚪 Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header-section">
            <div class="header-title">
                <h1>Dashboard</h1>
                <p>Welcome back to your inventory management system.</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-size: 13px; font-weight: 600; color: #0f172a;">admin</div>
                    <div style="font-size: 11px; color: #64748b;">Administrator</div>
                </div>
                <div class="user-avatar">A</div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon icon-blue">📦</div>
                <div>
                    <div class="metric-label">Total Products</div>
                    <div class="metric-value"><?php echo $total_products; ?></div>
                    <div class="metric-sub">Products in inventory</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon icon-green">💰</div>
                <div>
                    <div class="metric-label">Total Sales</div>
                    <div class="metric-value">₹<?php echo number_format($total_sales, 2); ?></div>
                    <div class="metric-sub">This month</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon icon-amber">🛒</div>
                <div>
                    <div class="metric-label">Total Purchases</div>
                    <div class="metric-value">₹0.00</div>
                    <div class="metric-sub">This month</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon icon-red">⚠️</div>
                <div>
                    <div class="metric-label">Low Stock</div>
                    <div class="metric-value"><?php echo $low_stock_count; ?></div>
                    <div class="metric-sub">Need attention</div>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="dashboard-grid">
           

            <!-- Low Stock Items / Empty State -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Low Stock</div>
                        <div class="card-subtitle">Products that need attention</div>
                    </div>
                    <a href="stock.php" class="card-link">View</a>
                </div>
                <?php if ($low_stock_items && $low_stock_items->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                    <td><span style="color: #dc2626; font-weight: 600;"><?php echo $item['quantity']; ?> left</span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <div class="empty-text">No low stock items</div>
                        <div class="empty-sub">Your inventory is looking good.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="quick-actions-card">
            <div class="card-title" style="margin-bottom: 12px;">Quick Actions</div>
            <p style="font-size: 13px; color: #64748b;">Frequently used inventory functions</p>
        </div>
    </div>
</body>
</html>