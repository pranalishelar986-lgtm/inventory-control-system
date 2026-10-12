<?php
session_start();
include 'db.php';

// Auto-fix missing date for Order #6 if empty
$conn->query("UPDATE sales SET sale_date = CURRENT_TIMESTAMP() WHERE id = 6 AND (sale_date IS NULL OR sale_date = '0000-00-00 00:00:00' OR sale_date = '')");

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_name = $_SESSION['client_name'];

// Fetch orders history joining sales and products tables
$query = "SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id = p.id WHERE s.customer_id = '$client_id' ORDER BY s.id DESC";
$orders = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Inventory System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background-color: #0f172a; color: #94a3b8; display: flex; flex-direction: column; justify-content: space-between; padding: 24px 16px; position: fixed; height: 100vh; }
        .sidebar-brand { font-size: 18px; font-weight: 700; color: #ffffff; padding: 0 12px 24px 12px; border-bottom: 1px solid #1e293b; display: flex; align-items: center; gap: 12px; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 16px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: #2563eb; color: #ffffff; }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        .header-title h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin-bottom: 24px; }
        
        /* Table Styles */
        .table-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 20px; font-size: 14px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; color: #475569; font-weight: 600; }
        td { color: #1e293b; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div style="width: 32px; height: 32px; background-color: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 16px;">📦</div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #ffffff; line-height: 1.2;">Client Portal</div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 400;">Inventory System</div>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="client_dashboard.php">Browse Products</a></li>
                <li><a href="client_orders.php" class="active">My Orders</a></li>
            </ul>
        </div>
        <div>
            <ul class="sidebar-menu" style="margin-bottom: 12px;">
                <li><a href="client_logout.php" style="color: #ef4444;">Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header-title">
            <h1>My Order History</h1>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td>₹<?php echo number_format($row['unit_price'] ?? 0, 2); ?></td>
                                <td><strong>₹<?php echo number_format($row['total'], 2); ?></strong></td>
                                <td><?php echo $row['sale_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 30px;">No orders placed yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>