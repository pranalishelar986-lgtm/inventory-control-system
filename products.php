<?php

require_once "includes/database.php";

/* ---------------------------------------
   PRODUCT DATA
---------------------------------------- */

$result = mysqli_query(
    $conn,
    "SELECT * FROM products ORDER BY id DESC"
);

/* ---------------------------------------
   STATISTICS
---------------------------------------- */

$total_products = 0;
$total_units = 0;
$low_stock = 0;
$out_of_stock = 0;

$stats_query = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total_products,
        COALESCE(SUM(quantity), 0) AS total_units,
        SUM(CASE
            WHEN quantity > 0 AND quantity <= min_stock
            THEN 1 ELSE 0
        END) AS low_stock,
        SUM(CASE
            WHEN quantity <= 0
            THEN 1 ELSE 0
        END) AS out_of_stock
     FROM products"
);

if ($stats_query) {

    $stats = mysqli_fetch_assoc($stats_query);

    $total_products = (int) $stats["total_products"];
    $total_units = (int) $stats["total_units"];
    $low_stock = (int) $stats["low_stock"];
    $out_of_stock = (int) $stats["out_of_stock"];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Products | Inventory Control System</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="app">


    <!-- =========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="logo">

            <div class="logo-icon">
                📦
            </div>

            <div>

                <h2>Inventory</h2>

                <span>
                    Control System
                </span>

            </div>

        </div>


        <nav class="menu">

            <a
                href="dashboard.php"
                class="menu-item"
            >
                <span>📊</span>
                Dashboard
            </a>


            <a
                href="products.php"
                class="menu-item active"
            >
                <span>📦</span>
                Products
            </a>


            <a
                href="categories.php"
                class="menu-item"
            >
                <span>🏷️</span>
                Categories
            </a>


            <a
                href="suppliers.php"
                class="menu-item"
            >
                <span>🚚</span>
                Suppliers
            </a>


            <a
                href="purchases.php"
                class="menu-item"
            >
                <span>🛒</span>
                Purchases
            </a>


            <a
                href="sales.php"
                class="menu-item"
            >
                <span>💰</span>
                Sales
            </a>


            <a
                href="stock.php"
                class="menu-item"
            >
                <span>📋</span>
                Stock
            </a>


            <a
                href="reports.php"
                class="menu-item"
            >
                <span>📈</span>
                Reports
            </a>


            <div class="menu-line"></div>


            <a
                href="settings.php"
                class="menu-item"
            >
                <span>⚙️</span>
                Settings
            </a>


            <a
                href="login.php"
                class="menu-item logout"
            >
                <span>🚪</span>
                Logout
            </a>

        </nav>

    </aside>



    <!-- =========================================
         MAIN CONTENT
    ========================================== -->

    <main class="main-content">


        <!-- TOP BAR -->

        <header class="topbar">

            <div>

                <h1>
                    Products
                </h1>

                <p>
                    Manage and monitor your inventory.
                </p>

            </div>

        </header>



        <!-- =====================================
             PAGE TITLE
        ====================================== -->

        <section class="products-intro">

            <div>

                <div class="products-breadcrumb">
                    Inventory
                    <span>/</span>
                    Products
                </div>

                <h2>
                    Product Management
                </h2>

                <p>
                    Keep track of products, stock levels,
                    pricing and availability.
                </p>

            </div>


            <a
                href="add-product.php"
                class="products-primary-btn"
            >

                <span class="btn-plus">
                    +
                </span>

                Add Product

            </a>

        </section>



        <!-- =====================================
             KPI CARDS
        ====================================== -->

        <section class="products-kpis">


            <!-- TOTAL PRODUCTS -->

            <div class="products-kpi">

                <div class="kpi-top">

                    <div class="kpi-icon purple">
                        📦
                    </div>

                    <span class="kpi-label">
                        Products
                    </span>

                </div>

                <div class="kpi-value">
                    <?php echo $total_products; ?>
                </div>

                <div class="kpi-footer">
                    Total products
                </div>

            </div>



            <!-- TOTAL UNITS -->

            <div class="products-kpi">

                <div class="kpi-top">

                    <div class="kpi-icon blue">
                        ◈
                    </div>

                    <span class="kpi-label">
                        Inventory
                    </span>

                </div>

                <div class="kpi-value">
                    <?php echo number_format($total_units); ?>
                </div>

                <div class="kpi-footer">
                    Units in stock
                </div>

            </div>



            <!-- LOW STOCK -->

            <div class="products-kpi">

                <div class="kpi-top">

                    <div class="kpi-icon orange">
                        !
                    </div>

                    <span class="kpi-label">
                        Attention
                    </span>

                </div>

                <div class="kpi-value">
                    <?php echo $low_stock; ?>
                </div>

                <div class="kpi-footer">
                    Low stock items
                </div>

            </div>



            <!-- OUT OF STOCK -->

            <div class="products-kpi">

                <div class="kpi-top">

                    <div class="kpi-icon red">
                        ×
                    </div>

                    <span class="kpi-label">
                        Out of Stock
                    </span>

                </div>

                <div class="kpi-value">
                    <?php echo $out_of_stock; ?>
                </div>

                <div class="kpi-footer">
                    Need restocking
                </div>

            </div>

        </section>



        <!-- =====================================
             PRODUCT TABLE
        ====================================== -->

        <section class="products-panel">


            <!-- PANEL HEADER -->

            <div class="products-panel-header">


                <div>

                    <h3>
                        All Products
                    </h3>

                    <p>
                        View and manage your inventory items.
                    </p>

                </div>


                <div class="products-toolbar">


                    <!-- SEARCH -->

                    <div class="products-search">

                        <span>
                            ⌕
                        </span>

                        <input
                            type="text"
                            id="productSearch"
                            placeholder="Search products..."
                        >

                    </div>


                    <!-- FILTER -->

                    <select
                        id="stockFilter"
                        class="products-filter"
                    >

                        <option value="all">
                            All Stock
                        </option>

                        <option value="in">
                            In Stock
                        </option>

                        <option value="low">
                            Low Stock
                        </option>

                        <option value="out">
                            Out of Stock
                        </option>

                    </select>

                </div>

            </div>



            <!-- TABLE -->

            <div class="products-table-container">

                <table
                    class="professional-products-table"
                    id="productsTable"
                >

                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Stock
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $result &&
                        mysqli_num_rows($result) > 0
                    ): ?>


                        <?php while (
                            $product =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <?php

                            $quantity =
                                (int) $product["quantity"];

                            $minimum =
                                (int) $product["min_stock"];


                            if ($quantity <= 0) {

                                $status =
                                    "out";

                                $status_text =
                                    "Out of stock";

                            } elseif (
                                $quantity <= $minimum
                            ) {

                                $status =
                                    "low";

                                $status_text =
                                    "Low stock";

                            } else {

                                $status =
                                    "in";

                                $status_text =
                                    "In stock";
                            }


                            $initial =
                                strtoupper(
                                    substr(
                                        $product["name"],
                                        0,
                                        1
                                    )
                                );

                            ?>


                            <tr
                                data-status="<?php
                                echo $status;
                                ?>"
                            >


                                <!-- PRODUCT -->

                                <td>

                                    <div class="product-cell">

                                        <div
                                            class="product-avatar-large"
                                        >
                                            <?php
                                            echo htmlspecialchars(
                                                $initial
                                            );
                                            ?>
                                        </div>


                                        <div
                                            class="product-cell-info"
                                        >

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $product["name"]
                                                );
                                                ?>

                                            </strong>

                                            <span>
                                                Product ID #
                                                <?php
                                                echo $product["id"];
                                                ?>
                                            </span>

                                        </div>

                                    </div>

                                </td>



                                <!-- SKU -->

                                <td>

                                    <span class="sku-text">

                                        <?php
                                        echo htmlspecialchars(
                                            $product["sku"]
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- CATEGORY -->

                                <td>

                                    <span
                                        class="category-text"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $product["category"]
                                            ?? "-"
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- PRICE -->

                                <td>

                                    <span class="price-text">

                                        ₹<?php
                                        echo number_format(
                                            $product["price"],
                                            2
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- STOCK -->

                                <td>

                                    <div class="stock-cell">

                                        <div
                                            class="stock-number"
                                        >

                                            <strong>
                                                <?php
                                                echo $quantity;
                                                ?>
                                            </strong>

                                            <span>
                                                units
                                            </span>

                                        </div>


                                        <div
                                            class="stock-progress"
                                        >

                                            <?php

                                            $percentage =
                                                $minimum > 0
                                                ? min(
                                                    100,
                                                    (
                                                        $quantity
                                                        /
                                                        ($minimum * 3)
                                                    ) * 100
                                                )
                                                : 100;

                                            ?>

                                            <div
                                                class="stock-progress-bar <?php
                                                echo $status;
                                                ?>"
                                                style="width: <?php
                                                echo $percentage;
                                                ?>%;"
                                            ></div>

                                        </div>

                                    </div>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="professional-status <?php
                                        echo $status;
                                        ?>"
                                    >

                                        <span
                                            class="status-dot"
                                        ></span>

                                        <?php
                                        echo $status_text;
                                        ?>

                                    </span>

                                </td>



                                <!-- ACTION -->

                                <td>

                                    <a
                                        href="edit-product.php?id=<?php
                                        echo $product["id"];
                                        ?>"
                                        class="professional-edit"
                                    >

                                        Edit

                                        <span>
                                            →
                                        </span>

                                    </a>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="7"
                                class="professional-empty"
                            >

                                <div>

                                    <div
                                        class="empty-product-icon"
                                    >
                                        📦
                                    </div>

                                    <h3>
                                        No products yet
                                    </h3>

                                    <p>
                                        Add your first product
                                        to start managing
                                        inventory.
                                    </p>

                                    <a
                                        href="add-product.php"
                                        class="products-primary-btn"
                                    >
                                        + Add Product
                                    </a>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>



            <!-- FOOTER -->

            <div class="products-panel-footer">

                <span>
                    Showing
                    <strong id="visibleCount">
                        <?php
                        echo $total_products;
                        ?>
                    </strong>
                    products
                </span>

                <span>
                    Inventory Control System
                </span>

            </div>


        </section>


    </main>

</div>



<!-- =========================================
     SEARCH + FILTER
========================================== -->

<script>

const searchInput =
    document.getElementById("productSearch");

const filter =
    document.getElementById("stockFilter");

const rows =
    document.querySelectorAll(
        "#productsTable tbody tr[data-status]"
    );

const visibleCount =
    document.getElementById("visibleCount");


function filterProducts() {

    const search =
        searchInput.value
            .toLowerCase()
            .trim();

    const selected =
        filter.value;

    let count = 0;


    rows.forEach(function(row) {

        const text =
            row.innerText.toLowerCase();

        const status =
            row.dataset.status;


        const matchesSearch =
            text.includes(search);


        const matchesFilter =
            selected === "all"
            ||
            status === selected;


        if (
            matchesSearch
            &&
            matchesFilter
        ) {

            row.style.display = "";

            count++;

        } else {

            row.style.display = "none";

        }

    });


    visibleCount.innerText = count;

}


searchInput.addEventListener(
    "input",
    filterProducts
);


filter.addEventListener(
    "change",
    filterProducts
);

</script>


</body>

</html>