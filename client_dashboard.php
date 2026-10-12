<?php
session_start();
include 'db.php';

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_name = $_SESSION['client_name'];

// Fetch products from database
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query("SELECT * FROM products");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard - Inventory System</title>
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
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header-title h1 { font-size: 26px; font-weight: 700; color: #0f172a; }
        .header-title p { font-size: 14px; color: #64748b; margin-top: 4px; }
        
        .user-profile { display: flex; align-items: center; gap: 12px; background: #ffffff; padding: 8px 16px; border-radius: 30px; border: 1px solid #e2e8f0; }
        .user-avatar { width: 36px; height: 36px; background-color: #2563eb; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }

        /* Search Bar */
        .search-bar { margin-bottom: 24px; display: flex; gap: 10px; }
        .search-bar input { padding: 10px 14px; width: 300px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        .search-bar button { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .search-bar button:hover { background: #1d4ed8; }

        /* Products Grid */
        .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .product-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .product-name { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 8px; }
        .product-price { font-size: 18px; font-weight: 700; color: #2563eb; margin-bottom: 8px; }
        .product-stock { font-size: 13px; color: #64748b; margin-bottom: 16px; }
        .btn-order { background: #16a34a; color: white; padding: 8px 12px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: block; }
        .btn-order:hover { background: #15803d; }
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
                <li><a href="client_dashboard.php" class="active">Browse Products</a></li>
                <li><a href="client_orders.php">My Orders</a></li>
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
        <div class="header-section">
            <div class="header-title">
                <h1>Welcome, <?php echo htmlspecialchars($client_name); ?>!</h1>
                <p>Browse available products from the inventory and place orders.</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-size: 13px; font-weight: 600; color: #0f172a;"><?php echo htmlspecialchars($client_name); ?></div>
                    <div style="font-size: 11px; color: #64748b;">Customer</div>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr($client_name, 0, 1)); ?></div>
            </div>
        </div>

        <!-- Search Bar -->
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search products by name..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Product Grid Display -->
        <div class="products-grid">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div>
                            <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div class="product-price">₹<?php echo number_format($row['selling_price'] ?? $row['price'] ?? 0, 2); ?></div>
                            <div class="product-stock">Available Stock: <strong><?php echo $row['quantity']; ?></strong> units</div>
                        </div>
                        <a href="client_order_item.php?id=<?php echo $row['id']; ?>" class="btn-order">Order Now</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #64748b;">No products found in inventory.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>