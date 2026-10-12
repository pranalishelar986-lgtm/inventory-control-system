<?php
// purchases.php

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// 2. Handle Form Submission for New Purchase
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = isset($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 0;
    $quantity    = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $unit_cost   = isset($_POST['unit_cost']) ? floatval($_POST['unit_cost']) : 0.00;
    $total_cost  = $quantity * $unit_cost;

    if ($supplier_id > 0 && $quantity > 0) {
        // Inserts using your exact DB column names: supplier_id, purchase_date, total
        $sql = "INSERT INTO purchases (supplier_id, purchase_date, total) VALUES ('$supplier_id', CURDATE(), '$total_cost')";

        if ($conn->query($sql) === TRUE) {
            $message = "Purchase order created successfully!";
        } else {
            $message = "Error adding purchase: " . $conn->error;
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// 3. Fetch Metrics Summary
$total_purchases_val = 0.00;
$total_orders_count = 0;

$sum_query = $conn->query("SELECT SUM(total) AS total_sum, COUNT(*) AS total_count FROM purchases");
if ($sum_query && $row = $sum_query->fetch_assoc()) {
    $total_purchases_val = $row['total_sum'] ?? 0.00;
    $total_orders_count  = $row['total_count'] ?? 0;
}

// 4. Fetch All Purchases joining with Suppliers table
$purchases_list = $conn->query("
    SELECT p.id, p.purchase_date, p.total, s.name AS supplier_name 
    FROM purchases p 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases - Inventory Control System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header-title h1 { font-size: 24px; font-weight: 700; color: #0f172a; }
        .header-title p { font-size: 14px; color: #64748b; margin-top: 4px; }
        .btn-add { background-color: #2563eb; color: #ffffff; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-add:hover { background-color: #1d4ed8; }
        
        .metrics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 32px; }
        .metric-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .metric-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-blue { background-color: #eff6ff; color: #2563eb; }
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
        
        .empty-state { text-align: center; padding: 48px 20px; }
        .empty-state p { color: #64748b; margin-top: 8px; font-size: 14px; }

        /* Modal Styles */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; }
        .modal-container { background: #ffffff; width: 500px; border-radius: 12px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .alert { padding: 12px 16px; background-color: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="header-section">
        <div class="header-title">
            <h1>Purchases</h1>
            <p>Manage your purchase orders and incoming stock inventory.</p>
        </div>
        <button class="btn-add" onclick="document.getElementById('addPoModal').style.display='flex'">+ Add Purchase</button>
    </div>

    <!-- Metrics Row -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon icon-blue">🛒</div>
            <div>
                <div class="metric-label">Total Purchases</div>
                <div class="metric-value">₹<?php echo number_format($total_purchases_val, 2); ?></div>
                <div class="metric-sub">Lifetime total</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-green">📦</div>
            <div>
                <div class="metric-label">Total Orders</div>
                <div class="metric-value"><?php echo $total_orders_count; ?></div>
                <div class="metric-sub">Recorded transactions</div>
            </div>
        </div>
    </div>

    <!-- Purchases Table Card -->
    <div class="content-card">
        <div class="card-header">
            <div>
                <h3 style="font-size: 18px; font-weight: 600;">Recent Purchases</h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Latest stock procurement transactions</p>
            </div>
            <input type="text" class="search-box" placeholder="Search orders...">
        </div>

        <?php if ($purchases_list && $purchases_list->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Supplier</th>
                        <th>Purchase Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $purchases_list->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($p['supplier_name'] ?? ('Supplier #' . $p['supplier_id'])); ?></strong></td>
                            <td><?php echo date("d M Y", strtotime($p['purchase_date'])); ?></td>
                            <td><strong>₹<?php echo number_format($p['total'], 2); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 32px; margin-bottom: 8px;">📦</div>
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b;">No purchases yet</h4>
                <p>Purchase orders and supplier transactions will appear here</p>
                <button class="btn-add" style="margin-top: 16px;" onclick="document.getElementById('addPoModal').style.display='flex'">+ Add Purchase</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Form -->
    <div id="addPoModal" class="modal-overlay">
        <div class="modal-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 600;">New Purchase Order</h3>
                <span style="cursor: pointer; color: #94a3b8; font-size: 20px;" onclick="document.getElementById('addPoModal').style.display='none'">&times;</span>
            </div>

            <form action="" method="POST">
                <!-- Supplier Field -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #334155; margin-bottom: 6px;">Supplier</label>
                    <select name="supplier_id" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; outline: none;" required>
                        <option value="">Select Supplier</option>
                        <?php
                        $suppliers_query = $conn->query("SELECT * FROM suppliers WHERE status='Active'");
                        if ($suppliers_query && $suppliers_query->num_rows > 0) {
                            while ($sup = $suppliers_query->fetch_assoc()) {
                                echo "<option value='" . $sup['id'] . "'>" . htmlspecialchars($sup['name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Product & Quantity -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #334155; margin-bottom: 6px;">Product</label>
                        <select name="product_id" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; outline: none;" required>
                            <option value="">Select Product</option>
                            <?php
                            $products_query = $conn->query("SELECT * FROM products");
                            if ($products_query && $products_query->num_rows > 0) {
                                while ($prod = $products_query->fetch_assoc()) {
                                    $prod_name = $prod['name'] ?? $prod['product_name'] ?? ('Product #' . $prod['id']);
                                    echo "<option value='" . $prod['id'] . "'>" . htmlspecialchars($prod_name) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #334155; margin-bottom: 6px;">Quantity</label>
                        <input type="number" name="quantity" min="1" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; outline: none;" required>
                    </div>
                </div>

                <!-- Unit Cost Field (Converted to Rupees ₹) -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #334155; margin-bottom: 6px;">Unit Cost (₹)</label>
                    <input type="number" name="unit_cost" step="0.01" placeholder="0.00" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; outline: none;" required>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="document.getElementById('addPoModal').style.display='none'" style="background: transparent; color: #64748b; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 18px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #2563eb; color: #ffffff; border: none; border-radius: 8px; padding: 10px 18px; font-weight: 500; cursor: pointer;">Create Purchase</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

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