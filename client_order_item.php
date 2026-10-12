<?php
session_start();
include 'db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_name = $_SESSION['client_name'];
$msg = "";
$error = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: client_dashboard.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: client_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = intval($_POST['quantity']);
    $unit_price = $product['selling_price'] ?? $product['price'] ?? 0;
    
    if ($quantity <= 0) {
        $error = "Please enter a valid quantity.";
    } elseif ($quantity > $product['quantity']) {
        $error = "Requested quantity exceeds available stock (" . $product['quantity'] . " available).";
    } else {
  $total = $quantity * $unit_price;
        $customer_id = $_SESSION['client_id']; // Get the client ID from session

        // Update query to include customer_id and sale_date
        $insert_stmt = $conn->prepare("INSERT INTO sales (product_id, customer_id, quantity, unit_price, total, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($insert_stmt) {
            // "iiidd" means: integer, integer, integer, double, double
            $insert_stmt->bind_param("iiidd", $product_id, $customer_id, $quantity, $unit_price, $total);
            
            if ($insert_stmt->execute()) {
                $new_stock = $product['quantity'] - $quantity;
                $update_stock = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
                if ($update_stock) {
                    $update_stock->bind_param("ii", $new_stock, $product_id);
                    $update_stock->execute();
                }
                
                $msg = "Order placed successfully! Total Amount: ₹" . number_format($total, 2);
                $product['quantity'] = $new_stock;
            } else {
                $error = "Failed to place order: " . $insert_stmt->error;
            }
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order - Inventory System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background-color: #0f172a; color: #94a3b8; display: flex; flex-direction: column; justify-content: space-between; padding: 24px 16px; position: fixed; height: 100vh; }
        .sidebar-brand { font-size: 18px; font-weight: 700; color: #ffffff; padding: 0 12px 24px 12px; border-bottom: 1px solid #1e293b; display: flex; align-items: center; gap: 12px; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 16px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: #2563eb; color: #ffffff; }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; max-width: 500px; }
        h2 { font-size: 20px; color: #0f172a; margin-bottom: 8px; }
        .price-tag { font-size: 22px; font-weight: 700; color: #2563eb; margin-bottom: 16px; }
        .stock-tag { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        label { font-size: 14px; font-weight: 600; color: #334155; display: block; margin-bottom: 8px; }
        input[type="number"] { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .btn-submit { background: #16a34a; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; width: 100%; cursor: pointer; font-size: 15px; }
        .btn-submit:hover { background: #15803d; }
        .btn-back { display: inline-block; margin-top: 16px; color: #2563eb; text-decoration: none; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
    </style>
</head>
<body>
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
    <div class="main-content">
        <div class="card">
            <h2>Place Order: <?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="price-tag">₹<?php echo number_format($product['selling_price'] ?? $product['price'] ?? 0, 2); ?></div>
            <div class="stock-tag">Available Stock: <strong><?php echo $product['quantity']; ?></strong> units</div>

            <?php if($msg): ?><div class="alert-success"><?php echo $msg; ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST">
                <label for="quantity">Order Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" value="1" required>
                <button type="submit" class="btn-submit">Confirm Order</button>
            </form>

            <a href="client_dashboard.php" class="btn-back">← Back to Products</a>
        </div>
    </div>
</body>
</html>