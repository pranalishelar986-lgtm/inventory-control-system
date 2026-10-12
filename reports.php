<?php
// reports.php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter inputs
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build SQL date condition
$date_condition = "";
if (!empty($start_date) && !empty($end_date)) {
    $date_condition = " WHERE s.sale_date BETWEEN '$start_date' AND '$end_date' ";
}

// 1. Inventory Asset Valuation
$inventory_value = 0.00;
$inv_query = $conn->query("SELECT SUM(price * quantity) AS total_val FROM products");
if ($inv_query && $row = $inv_query->fetch_assoc()) {
    $inventory_value = $row['total_val'] ?? 0.00;
}

// 2. Total Revenue (Filtered by date if selected)
$total_revenue = 0.00;
$rev_query = $conn->query("SELECT SUM(s.total) AS revenue FROM sales s" . $date_condition);
if ($rev_query && $row = $rev_query->fetch_assoc()) {
    $total_revenue = $row['revenue'] ?? 0.00;
}

// 3. Purchase Expense
$purchase_expense = 0.00;

// 4. Reorder Alert Count
$reorder_count = 0;
$alert_query = $conn->query("SELECT COUNT(*) AS count FROM products WHERE quantity <= min_stock_level");
if ($alert_query && $row = $alert_query->fetch_assoc()) {
    $reorder_count = $row['count'] ?? 0;
}

// 5. Fetch Sales Report Log (Filtered)
$sales_query = "SELECT s.*, p.name AS product_name FROM sales s LEFT JOIN products p ON s.product_id = p.id";
if (!empty($date_condition)) {
    $sales_query .= $date_condition;
}
$sales_query .= " ORDER BY s.id DESC";
$sales_result = $conn->query($sales_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Inventory Control System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header-title h1 { font-size: 24px; font-weight: 700; color: #0f172a; }
        .header-title p { font-size: 14px; color: #64748b; margin-top: 4px; }
        
        .btn-group { display: flex; gap: 10px; }
        .btn-action { background: #ffffff; border: 1px solid #cbd5e1; color: #334155; padding: 9px 16px; border-radius: 8px; font-weight: 500; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; }
        .btn-action:hover { background: #f1f5f9; }
        .btn-primary { background-color: #2563eb; color: #ffffff; border: none; }
        .btn-primary:hover { background-color: #1d4ed8; }

        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
        .metric-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .metric-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-purple { background-color: #f5f3ff; color: #7c3aed; }
        .icon-blue { background-color: #eff6ff; color: #2563eb; }
        .icon-gray { background-color: #f1f5f9; color: #475569; }
        .icon-amber { background-color: #fffbeb; color: #d97706; }
        .metric-label { font-size: 13px; color: #64748b; font-weight: 500; }
        .metric-value { font-size: 20px; font-weight: 700; color: #0f172a; margin: 2px 0; }
        .metric-sub { font-size: 12px; color: #94a3b8; }

        .content-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .filter-form { display: flex; gap: 12px; align-items: center; margin-bottom: 20px; background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .filter-form label { font-size: 13px; font-weight: 500; color: #475569; }
        .filter-form input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; }
        
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-box { padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 240px; outline: none; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        
        .empty-state { text-align: center; padding: 48px 20px; }
        .empty-state p { color: #64748b; margin-top: 8px; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header-section">
        <div class="header-title">
            <h1>Reports & Analytics</h1>
            <p>Generate financial summaries, stock movements, and performance insights.</p>
        </div>
        <div class="btn-group">
            <button class="btn-action" onclick="window.print()">Export PDF</button>
            <a href="export_excel.php" class="btn-action btn-primary">Export CSV / Excel</a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon icon-purple">📊</div>
            <div>
                <div class="metric-label">Inventory Value</div>
                <div class="metric-value">₹<?php echo number_format($inventory_value, 2); ?></div>
                <div class="metric-sub">Total asset value</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-blue">📈</div>
            <div>
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">₹<?php echo number_format($total_revenue, 2); ?></div>
                <div class="metric-sub"><?php echo (!empty($start_date)) ? 'Filtered period' : 'Lifetime total'; ?></div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-gray">🛒</div>
            <div>
                <div class="metric-label">Purchase Expense</div>
                <div class="metric-value">₹<?php echo number_format($purchase_expense, 2); ?></div>
                <div class="metric-sub">Procurement cost</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon icon-amber">⚠️</div>
            <div>
                <div class="metric-label">Reorder Alert Count</div>
                <div class="metric-value"><?php echo $reorder_count; ?></div>
                <div class="metric-sub">Items below min level</div>
            </div>
        </div>
    </div>

    <!-- Reports Log Card -->
    <div class="content-card">
        <!-- Date Filter Form -->
        <form method="GET" action="" class="filter-form">
            <span style="font-weight: 600; font-size: 13px; color: #1e293b; margin-right: 4px;">🔍 Filter by Date:</span>
            <label>From:</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            <label>To:</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            <button type="submit" class="btn-action btn-primary" style="padding: 7px 14px; font-size: 13px;">Apply Filter</button>
            <?php if (!empty($start_date)): ?>
                <a href="reports.php" class="btn-action" style="padding: 7px 14px; font-size: 13px; text-decoration: none;">Reset</a>
            <?php endif; ?>
        </form>

        <div class="card-header">
            <div>
                <h3 style="font-size: 18px; font-weight: 600;">Sales Report Log</h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Itemized history of recorded transactions</p>
            </div>
            <input type="text" class="search-box" placeholder="Search report...">
        </div>

        <?php if ($sales_result && $sales_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Unit Price</th>
                        <th>Sale Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sales_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['product_name'] ?? ('Product #' . $row['product_id'])); ?></strong></td>
                            <td><?php echo $row['quantity']; ?> units</td>
                            <td>₹<?php echo number_format($row['unit_price'], 2); ?></td>
                            <td><?php echo date("d M Y", strtotime($row['sale_date'])); ?></td>
                            <td><strong>₹<?php echo number_format($row['total'], 2); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 32px; margin-bottom: 8px;">📄</div>
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b;">No transactions found</h4>
                <p>No sales match the selected date range</p>
            </div>
        <?php endif; ?>
    </div>

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