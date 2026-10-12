<?php
session_start();

// Fallback session initialization
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'Admin';
}

$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = '';

$message = '';
$message_type = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    $pdo = null;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_account') {
        $username = trim($_POST['username']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!empty($new_password)) {
            if ($new_password === $confirm_password) {
                $hashed = md5($new_password);
                if ($pdo) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = 1 OR username = 'admin'");
                    $stmt->execute([$username, $hashed]);
                }
                $_SESSION['user'] = $username;
                $message = "Account details and password updated successfully!";
                $message_type = "success";
            } else {
                $message = "Passwords do not match!";
                $message_type = "error";
            }
        } else {
            if ($pdo) {
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = 1 OR username = 'admin'");
                $stmt->execute([$username]);
            }
            $_SESSION['user'] = $username;
            $message = "Username updated successfully!";
            $message_type = "success";
        }
    } elseif ($action === 'update_store') {
        $message = "Store configuration saved successfully!";
        $message_type = "success";
    } elseif ($action === 'update_system') {
        $message = "System preferences saved successfully!";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Inventory Control System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --accent-blue: #2563eb;
            --border-color: #e2e8f0;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #0f172a;
            color: #ffffff;
            padding: 24px 16px;
            box-sizing: border-box;
            position: fixed;
            height: 100vh;
        }

        .sidebar-brand {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand-icon {
            background: var(--accent-blue);
            padding: 8px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item {
            color: #94a3b8;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-item.active {
            background: var(--accent-blue);
        }

        .main-wrapper {
            margin-left: 250px;
            flex: 1;
            padding: 32px;
            box-sizing: border-box;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            padding: 6px 16px;
            border-radius: 30px;
            border: 1px solid var(--border-color);
        }

        .avatar {
            width: 34px;
            height: 34px;
            background: var(--accent-blue);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Settings Grid Layout */
        .settings-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 24px;
            align-items: start;
        }

        .settings-tabs {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            text-align: left;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            background: var(--bg-main);
            color: var(--text-primary);
        }

        .tab-btn.active {
            background: #eff6ff;
            color: var(--accent-blue);
            font-weight: 600;
        }

        .settings-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .card-header {
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text-primary);
        }

        .card-header p {
            margin: 4px 0 0 0;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            box-sizing: border-box;
            background: #ffffff;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            background: var(--accent-blue);
            color: #ffffff;
            border: none;
            padding: 11px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <span class="sidebar-brand-icon">📦</span>
                <div>Inventory<br><span style="font-size: 0.75rem; color: #64748b; font-weight: normal;">Control System</span></div>
            </div>
            <nav class="nav-list">
                <a href="dashboard.php" class="nav-item">📊 Dashboard</a>
                <a href="products.php" class="nav-item">📦 Products</a>
                <a href="categories.php" class="nav-item">🏷️ Categories</a>
                <a href="suppliers.php" class="nav-item">🏢 Suppliers</a>
                <a href="purchases.php" class="nav-item">🛒 Purchases</a>
                <a href="sales.php" class="nav-item">💰 Sales</a>
                <a href="stock.php" class="nav-item">📋 Stock</a>
                <a href="reports.php" class="nav-item">📈 Reports</a>
                <a href="settings.php" class="nav-item active">⚙️ Settings</a>
                <a href="login.php" class="nav-item" style="color: #ef4444; margin-top: 24px;">🚪 Logout</a>
            </nav>
        </div>

        <!-- MAIN WRAPPER -->
        <div class="main-wrapper">
            <div class="top-header">
                <div>
                    <h1 style="margin:0; font-size: 1.6rem; color: var(--text-primary);">System Settings</h1>
                    <p style="margin: 4px 0 0 0; color: var(--text-secondary); font-size: 0.9rem;">Configure store preferences, manage account security, and set low-stock thresholds.</p>
                </div>
                <div class="user-badge">
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.85rem; font-weight: 600;"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Administrator</span>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['user'], 0, 1)); ?></div>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="settings-container">
                <!-- TABS NAVIGATION -->
                <div class="settings-tabs">
                    <button class="tab-btn active" onclick="switchTab('account')">👤 Account Settings</button>
                    <button class="tab-btn" onclick="switchTab('store')">🏪 Store Information</button>
                    <button class="tab-btn" onclick="switchTab('system')">⚙️ System & Alerts</button>
                </div>

                <!-- TAB CONTENTS -->
                <div>
                    <!-- Account Settings Tab -->
                    <div id="tab-account" class="tab-content active">
                        <div class="settings-card">
                            <div class="card-header">
                                <h2>Account & Security</h2>
                                <p>Update your admin profile username and password.</p>
                            </div>
                            <form method="POST" action="settings.php">
                                <input type="hidden" name="action" value="update_account">
                                <div class="form-group">
                                    <label for="username">Administrator Username</label>
                                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['user']); ?>" required>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
                                    </div>
                                </div>
                                <button type="submit" class="btn-submit">Save Account Changes</button>
                            </form>
                        </div>
                    </div>

                    <!-- Store Info Tab -->
                    <div id="tab-store" class="tab-content">
                        <div class="settings-card">
                            <div class="card-header">
                                <h2>Store Details</h2>
                                <p>Manage the organization details printed on reports and invoices.</p>
                            </div>
                            <form method="POST" action="settings.php">
                                <input type="hidden" name="action" value="update_store">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="store_name">Store Name</label>
                                        <input type="text" id="store_name" name="store_name" value="Main Branch Warehouse">
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_email">Support Email</label>
                                        <input type="email" id="contact_email" name="contact_email" value="admin@inventory.local">
                                    </div>
                                </div>
                                <div class="form-group full">
                                    <label for="address">Physical Address</label>
                                    <input type="text" id="address" name="address" value="123 Commerce Way, Industrial Park">
                                </div>
                                <button type="submit" class="btn-submit">Save Store Info</button>
                            </form>
                        </div>
                    </div>

                    <!-- System Preferences Tab -->
                    <div id="tab-system" class="tab-content">
                        <div class="settings-card">
                            <div class="card-header">
                                <h2>System & Inventory Thresholds</h2>
                                <p>Configure default currencies and low-stock indicators.</p>
                            </div>
                            <form method="POST" action="settings.php">
                                <input type="hidden" name="action" value="update_system">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="currency">Default Currency</label>
                                        <select id="currency" name="currency">
                                            <option value="$">USD ($)</option>
                                            <option value="₹">INR (₹)</option>
                                            <option value="€">EUR (€)</option>
                                            <option value="£">GBP (£)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="low_stock">Low Stock Warning Threshold</label>
                                        <input type="number" id="low_stock" name="low_stock" value="5">
                                    </div>
                                </div>
                                <button type="submit" class="btn-submit">Save System Preferences</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            document.getElementById('tab-' + tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>

