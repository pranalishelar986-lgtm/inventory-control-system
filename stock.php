<?php
// stock.php

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Fetch Metrics Summary from products table
$total_stock_count = 0;
$low_stock_count = 0;
$out_of_stock_count = 0;
$total_stock_value = 0.00;

$metrics_query = $conn->query("
    SELECT 
        SUM(quantity) AS total_qty, 
        SUM(price * quantity) AS total_val,
        SUM(CASE WHEN quantity <= 5 AND quantity > 0 THEN 1 ELSE 0 END) AS low, 
        SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_stock 
    FROM products
");

if ($metrics_query && $row = $metrics_query->fetch_assoc()) {
    $total_stock_count  = $row['total_qty'] ?? 0;
    $total_stock_value  = $row['total_val'] ?? 0.00;
    $low_stock_count    = $row['low'] ?? 0;
    $out_of_stock_count = $row['out_stock'] ?? 0;
}

// 3. Fetch All Products with Categories
$stock_list = $conn->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - Inventory Control System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header-title h1 { font-size: 24px; font-weight: 700; color: #0f172a; }
        .header-title p { font-size: 14px; color: #64748b; margin-top: 4px; }
        .btn-add { background-color: #2563eb; color: #ffffff; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-add:hover { background-color: #1d4ed8; }
        
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
        .metric-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .metric-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-blue { background-color: #eff6ff; color: #2563eb; }
        .icon-amber { background-color: #fffbeb; color: #d97706; }
        .icon-red { background-color: #fef2f2; color: #dc2626; }
        .icon-green { background-color: #f0fdf4; color: #16a34a; }
        .metric-label { font-size: 13px; color: #64748b; font-weight: 500; }
        .metric-value { font-size: 22px; font-weight: 700; color: #0f172a; margin: 2px 0; }
        .metric-sub { font-size: 12px; color: #94a3b8; }

        .content-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-box { padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 240px; outline: none; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .badge-in { background-color: #f0fdf4; color: #16a34a; }
        .badge-low { background-color: #fffbeb; color: #d97706; }
        .badge-out { background-color: #fef2f2; color: #dc2626; }

        .empty-state { text-align: center; padding: 48px 20px; }
        .empty-state p { color: #64748b; margin-top: 8px; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header-section">
        <div class="header-title">
            <h1>Stock Management</h1>
            <p>Monitor real-time stock levels, record manual adjustments, and manage reorder points.</p>
        </div>
        <button class="btn-add">+ Adjust Stock</button>
    </div>

    <!-- Metrics Row -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon icon-blue">📦</div>
            <div>
                <div class="metric-label">Total Stock Count</div>
                <div class="metric-value"><?php echo $total_stock_count; ?> units</div>
                <div class="metric-sub">Across all categories</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-amber">⚠️</div>
            <div>
                <div class="metric-label">Low Stock Alerts</div>
                <div class="metric-value"><?php echo $low_stock_count; ?></div>
                <div class="metric-sub">Below reorder threshold</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-red">🚫</div>
            <div>
                <div class="metric-label">Out of Stock</div>
                <div class="metric-value"><?php echo $out_of_stock_count; ?></div>
                <div class="metric-sub">Needs immediate reorder</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-green">💰</div>
            <div>
                <div class="metric-label">Total Stock Value</div>
                <div class="metric-value">₹<?php echo number_format($total_stock_value, 2); ?></div>
                <div class="metric-sub">Inventory valuation</div>
            </div>
        </div>
    </div>

    <!-- Stock Table Card -->
    <div class="content-card">
        <div class="card-header">
            <div>
                <h3 style="font-size: 18px; font-weight: 600;">Inventory Stock Status</h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Detailed stock breakdown per product SKU</p>
            </div>
            <input type="text" class="search-box" placeholder="Search stock...">
        </div>

        <?php if ($stock_list && $stock_list->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Current Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $stock_list->fetch_assoc()): 
                        $qty = intval($item['quantity'] ?? 0);
                        if ($qty == 0) {
                            $status_badge = '<span class="badge badge-out">Out of stock</span>';
                        } elseif ($qty <= 5) {
                            $status_badge = '<span class="badge badge-low">Low stock</span>';
                        } else {
                            $status_badge = '<span class="badge badge-in">In stock</span>';
                        }
                    ?>
                        <tr>
                            <td>#<?php echo $item['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></td>
                            <td>₹<?php echo number_format($item['price'] ?? 0, 2); ?></td>
                            <td><strong><?php echo $qty; ?> units</strong></td>
                            <td><?php echo $status_badge; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 32px; margin-bottom: 8px;">📦</div>
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b;">No stock records found</h4>
                <p>Add products to view and manage inventory quantities here</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Search Filtering Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.querySelector('.search-box');
        const tableRows = document.querySelectorAll('table tbody tr');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
    </script>
</body>
</html>