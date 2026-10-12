<?php

require_once "includes/database.php";


/* =========================================
   SUPPLIER DATA
========================================= */

$result = mysqli_query(
    $conn,
    "SELECT * FROM suppliers ORDER BY id DESC"
);


/* =========================================
   STATISTICS
========================================= */

$total_suppliers = 0;
$active_suppliers = 0;
$inactive_suppliers = 0;

$stats_query = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total_suppliers,

        SUM(
            CASE
                WHEN status = 'Active'
                THEN 1
                ELSE 0
            END
        ) AS active_suppliers,

        SUM(
            CASE
                WHEN status = 'Inactive'
                THEN 1
                ELSE 0
            END
        ) AS inactive_suppliers

     FROM suppliers"
);


if ($stats_query) {

    $stats = mysqli_fetch_assoc(
        $stats_query
    );

    $total_suppliers =
        (int) $stats["total_suppliers"];

    $active_suppliers =
        (int) $stats["active_suppliers"];

    $inactive_suppliers =
        (int) $stats["inactive_suppliers"];
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

    <title>
        Suppliers | Inventory Control System
    </title>

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

                <h2>
                    Inventory
                </h2>

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
                class="menu-item"
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
                class="menu-item active"
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
                    Suppliers
                </h1>

                <p>
                    Manage your suppliers and vendor information.
                </p>

            </div>

        </header>



        <!-- =====================================
             PAGE INTRO
        ====================================== -->

        <section class="suppliers-intro">


            <div>

                <div class="suppliers-breadcrumb">

                    Inventory

                    <span>/</span>

                    Suppliers

                </div>


                <h2>
                    Supplier Management
                </h2>


                <p>
                    Keep supplier contact information
                    organized and up to date.
                </p>

            </div>


            <a
                href="add-supplier.php"
                class="suppliers-primary-btn"
            >

                <span>
                    +
                </span>

                Add Supplier

            </a>


        </section>



        <!-- =====================================
             KPI CARDS
        ====================================== -->

        <section class="suppliers-kpis">


            <!-- TOTAL -->

            <div class="suppliers-kpi">

                <div class="supplier-kpi-top">

                    <div class="supplier-kpi-icon purple">
                        🚚
                    </div>

                    <span>
                        Suppliers
                    </span>

                </div>


                <strong>
                    <?php
                    echo $total_suppliers;
                    ?>
                </strong>


                <small>
                    Total suppliers
                </small>

            </div>



            <!-- ACTIVE -->

            <div class="suppliers-kpi">

                <div class="supplier-kpi-top">

                    <div class="supplier-kpi-icon green">
                        ✓
                    </div>

                    <span>
                        Active
                    </span>

                </div>


                <strong>
                    <?php
                    echo $active_suppliers;
                    ?>
                </strong>


                <small>
                    Active suppliers
                </small>

            </div>



            <!-- INACTIVE -->

            <div class="suppliers-kpi">

                <div class="supplier-kpi-top">

                    <div class="supplier-kpi-icon orange">
                        !
                    </div>

                    <span>
                        Inactive
                    </span>

                </div>


                <strong>
                    <?php
                    echo $inactive_suppliers;
                    ?>
                </strong>


                <small>
                    Inactive suppliers
                </small>

            </div>


        </section>



        <!-- =====================================
             SUPPLIER PANEL
        ====================================== -->

        <section class="suppliers-panel">


            <!-- PANEL HEADER -->

            <div class="suppliers-panel-header">


                <div>

                    <h3>
                        All Suppliers
                    </h3>

                    <p>
                        View and manage your supplier contacts.
                    </p>

                </div>


                <div class="suppliers-tools">


                    <!-- SEARCH -->

                    <div class="suppliers-search">

                        <span>
                            ⌕
                        </span>

                        <input
                            type="text"
                            id="supplierSearch"
                            placeholder="Search suppliers..."
                        >

                    </div>


                    <!-- FILTER -->

                    <select
                        id="supplierFilter"
                        class="suppliers-filter"
                    >

                        <option value="all">
                            All Status
                        </option>

                        <option value="Active">
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>


                </div>

            </div>



            <!-- TABLE -->

            <div class="suppliers-table-container">


                <table
                    class="professional-suppliers-table"
                    id="suppliersTable"
                >

                    <thead>

                        <tr>

                            <th>
                                Supplier
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Address
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
                            $supplier =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <?php

                            $supplier_name =
                                $supplier["name"];

                            $initial =
                                strtoupper(
                                    substr(
                                        $supplier_name,
                                        0,
                                        1
                                    )
                                );

                            $status =
                                $supplier["status"]
                                ?? "Active";

                            ?>


                            <tr
                                data-status="<?php
                                echo htmlspecialchars(
                                    $status
                                );
                                ?>"
                            >


                                <!-- SUPPLIER -->

                                <td>

                                    <div
                                        class="supplier-cell"
                                    >


                                        <div
                                            class="supplier-avatar"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $initial
                                            );
                                            ?>

                                        </div>


                                        <div
                                            class="supplier-info"
                                        >

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $supplier_name
                                                );
                                                ?>

                                            </strong>


                                            <span>

                                                Supplier ID #

                                                <?php
                                                echo $supplier["id"];
                                                ?>

                                            </span>

                                        </div>

                                    </div>

                                </td>



                                <!-- PHONE -->

                                <td>

                                    <a
                                        href="tel:<?php
                                        echo htmlspecialchars(
                                            $supplier["phone"]
                                        );
                                        ?>"
                                        class="supplier-phone"
                                    >

                                        ☎

                                        <?php
                                        echo htmlspecialchars(
                                            $supplier["phone"]
                                        );
                                        ?>

                                    </a>

                                </td>



                                <!-- EMAIL -->

                                <td>

                                    <a
                                        href="mailto:<?php
                                        echo htmlspecialchars(
                                            $supplier["email"]
                                        );
                                        ?>"
                                        class="supplier-email"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $supplier["email"]
                                        );
                                        ?>

                                    </a>

                                </td>



                                <!-- ADDRESS -->

                                <td>

                                    <span
                                        class="supplier-address"
                                    >

                                        <?php

                                        echo !empty(
                                            $supplier["address"]
                                        )
                                        ? htmlspecialchars(
                                            $supplier["address"]
                                        )
                                        : "No address";

                                        ?>

                                    </span>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="supplier-status <?php
                                        echo strtolower(
                                            $status
                                        );
                                        ?>"
                                    >

                                        <span
                                            class="supplier-status-dot"
                                        ></span>

                                        <?php
                                        echo htmlspecialchars(
                                            $status
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- ACTION -->

                                <td>

                                    <a
                                        href="edit-supplier.php?id=<?php
                                        echo $supplier["id"];
                                        ?>"
                                        class="supplier-edit-btn"
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
                                colspan="6"
                                class="supplier-empty"
                            >

                                <div>

                                    <div
                                        class="supplier-empty-icon"
                                    >
                                        🚚
                                    </div>


                                    <h3>
                                        No suppliers yet
                                    </h3>


                                    <p>
                                        Add your first supplier
                                        to start managing vendors.
                                    </p>


                                    <a
                                        href="add-supplier.php"
                                        class="suppliers-primary-btn"
                                    >

                                        + Add Supplier

                                    </a>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>



            <!-- FOOTER -->

            <div class="suppliers-panel-footer">

                <span>

                    Showing

                    <strong id="supplierVisibleCount">

                        <?php
                        echo $total_suppliers;
                        ?>

                    </strong>

                    suppliers

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
========================================= -->

<script>

const supplierSearch =
    document.getElementById(
        "supplierSearch"
    );

const supplierFilter =
    document.getElementById(
        "supplierFilter"
    );

const supplierRows =
    document.querySelectorAll(
        "#suppliersTable tbody tr[data-status]"
    );

const supplierVisibleCount =
    document.getElementById(
        "supplierVisibleCount"
    );


function filterSuppliers() {

    const search =
        supplierSearch.value
            .toLowerCase()
            .trim();

    const selected =
        supplierFilter.value;

    let count = 0;


    supplierRows.forEach(function(row) {

        const text =
            row.innerText.toLowerCase();

        const status =
            row.dataset.status;


        const matchesSearch =
            text.includes(search);


        const matchesStatus =
            selected === "all"
            ||
            status === selected;


        if (
            matchesSearch
            &&
            matchesStatus
        ) {

            row.style.display = "";

            count++;

        } else {

            row.style.display = "none";

        }

    });


    supplierVisibleCount.innerText =
        count;

}


supplierSearch.addEventListener(
    "input",
    filterSuppliers
);


supplierFilter.addEventListener(
    "change",
    filterSuppliers
);

</script>


</body>

</html>