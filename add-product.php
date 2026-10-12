<?php

session_start();

require_once "includes/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values
    $name = trim($_POST["name"] ?? "");
    $sku = trim($_POST["sku"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = (float) ($_POST["price"] ?? 0);
    $quantity = (int) ($_POST["quantity"] ?? 0);
    $min_stock = (int) ($_POST["min_stock"] ?? 5);

    // Check required fields
    if ($name === "" || $sku === "") {

        $error = "Product name and SKU are required.";

    } else {

        // Check if SKU already exists
        $sql = "SELECT id FROM products WHERE sku = ?";

        $check = mysqli_prepare($conn, $sql);

        if (!$check) {
            die("Database error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($check, "s", $sku);

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $error = "This SKU already exists.";

            mysqli_stmt_close($check);

        } else {

            mysqli_stmt_close($check);

            // Insert product
            $sql = "INSERT INTO products
                    (name, sku, category, price, quantity, min_stock)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                die("Insert database error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sssddi",
                $name,
                $sku,
                $category,
                $price,
                $quantity,
                $min_stock
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: products.php");
                exit();

            } else {

                $error = "Unable to add product: " . mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Product</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="app">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">

            <div class="logo-icon">
                📦
            </div>

            <div>
                <h2>Inventory</h2>
                <span>Control System</span>
            </div>

        </div>


        <nav class="menu">

            <a href="dashboard.php" class="menu-item">
                <span>📊</span>
                Dashboard
            </a>

            <a href="products.php" class="menu-item active">
                <span>📦</span>
                Products
            </a>

            <a href="categories.php" class="menu-item">
                <span>🏷️</span>
                Categories
            </a>

            <a href="suppliers.php" class="menu-item">
                <span>🚚</span>
                Suppliers
            </a>

            <a href="purchases.php" class="menu-item">
                <span>🛒</span>
                Purchases
            </a>

            <a href="sales.php" class="menu-item">
                <span>💰</span>
                Sales
            </a>

            <a href="stock.php" class="menu-item">
                <span>📋</span>
                Stock
            </a>

            <a href="reports.php" class="menu-item">
                <span>📈</span>
                Reports
            </a>

            <div class="menu-line"></div>

            <a href="settings.php" class="menu-item">
                <span>⚙️</span>
                Settings
            </a>

            <a href="login.php" class="menu-item logout">
                <span>🚪</span>
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>Add Product</h1>

                <p>
                    Add a new product to your inventory.
                </p>

            </div>

        </header>


        <?php if ($error !== ""): ?>

            <div class="alert error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <section class="form-card">

            <form method="POST">

                <div class="form-grid">


                    <!-- PRODUCT NAME -->

                    <div class="form-group">

                        <label>
                            Product Name *
                        </label>

                        <input
                            type="text"
                            name="name"
                            placeholder="Enter product name"
                            required
                        >

                    </div>


                    <!-- SKU -->

                    <div class="form-group">

                        <label>
                            SKU *
                        </label>

                        <input
                            type="text"
                            name="sku"
                            placeholder="Example: PROD-001"
                            required
                        >

                    </div>


                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label>
                            Category
                        </label>

                        <input
                            type="text"
                            name="category"
                            placeholder="Example: Electronics"
                        >

                    </div>


                    <!-- PRICE -->

                    <div class="form-group">

                        <label>
                            Price *
                        </label>

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                        >

                    </div>


                    <!-- QUANTITY -->

                    <div class="form-group">

                        <label>
                            Quantity *
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            min="0"
                            placeholder="0"
                            required
                        >

                    </div>


                    <!-- MINIMUM STOCK -->

                    <div class="form-group">

                        <label>
                            Minimum Stock
                        </label>

                        <input
                            type="number"
                            name="min_stock"
                            min="0"
                            value="5"
                        >

                    </div>

                </div>


                <div class="form-actions">

                    <a
                        href="products.php"
                        class="btn-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Save Product
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>

</html>