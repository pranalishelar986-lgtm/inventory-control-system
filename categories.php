<?php

require_once "includes/database.php";

/* ---------------------------------------
   CATEGORY DATA
---------------------------------------- */

$result = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY id DESC"
);


/* ---------------------------------------
   STATISTICS
---------------------------------------- */

$total_categories = 0;
$active_categories = 0;
$inactive_categories = 0;

$stats_query = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total_categories,
        SUM(CASE
            WHEN status = 'Active'
            THEN 1 ELSE 0
        END) AS active_categories,
        SUM(CASE
            WHEN status = 'Inactive'
            THEN 1 ELSE 0
        END) AS inactive_categories
     FROM categories"
);

if ($stats_query) {

    $stats = mysqli_fetch_assoc($stats_query);

    $total_categories =
        (int) $stats["total_categories"];

    $active_categories =
        (int) $stats["active_categories"];

    $inactive_categories =
        (int) $stats["inactive_categories"];
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
        Categories | Inventory Control System
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
                class="menu-item active"
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
                    Categories
                </h1>

                <p>
                    Organize and manage your product categories.
                </p>

            </div>

        </header>



        <!-- =====================================
             PAGE INTRO
        ====================================== -->

        <section class="categories-intro">


            <div>

                <div class="categories-breadcrumb">

                    Inventory

                    <span>/</span>

                    Categories

                </div>


                <h2>
                    Category Management
                </h2>


                <p>
                    Create and manage categories
                    to keep your inventory organized.
                </p>

            </div>


            <button
                type="button"
                class="categories-primary-btn"
                onclick="openCategoryModal()"
            >

                <span>
                    +
                </span>

                Add Category

            </button>


        </section>



        <!-- =====================================
             KPI CARDS
        ====================================== -->

        <section class="categories-kpis">


            <!-- TOTAL -->

            <div class="categories-kpi">

                <div class="category-kpi-top">

                    <div class="category-kpi-icon purple">
                        🏷️
                    </div>

                    <span>
                        Categories
                    </span>

                </div>


                <strong>
                    <?php
                    echo $total_categories;
                    ?>
                </strong>


                <small>
                    Total categories
                </small>

            </div>



            <!-- ACTIVE -->

            <div class="categories-kpi">

                <div class="category-kpi-top">

                    <div class="category-kpi-icon green">
                        ✓
                    </div>

                    <span>
                        Active
                    </span>

                </div>


                <strong>
                    <?php
                    echo $active_categories;
                    ?>
                </strong>


                <small>
                    Active categories
                </small>

            </div>



            <!-- INACTIVE -->

            <div class="categories-kpi">

                <div class="category-kpi-top">

                    <div class="category-kpi-icon orange">
                        !
                    </div>

                    <span>
                        Inactive
                    </span>

                </div>


                <strong>
                    <?php
                    echo $inactive_categories;
                    ?>
                </strong>


                <small>
                    Inactive categories
                </small>

            </div>


        </section>



        <!-- =====================================
             CATEGORY PANEL
        ====================================== -->

        <section class="categories-panel">


            <!-- PANEL HEADER -->

            <div class="categories-panel-header">


                <div>

                    <h3>
                        All Categories
                    </h3>

                    <p>
                        View and manage your inventory categories.
                    </p>

                </div>


                <div class="categories-tools">


                    <div class="categories-search">

                        <span>
                            ⌕
                        </span>

                        <input
                            type="text"
                            id="categorySearch"
                            placeholder="Search categories..."
                        >

                    </div>


                    <select
                        id="categoryFilter"
                        class="categories-filter"
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

            <div class="categories-table-container">


                <table
                    class="professional-categories-table"
                    id="categoriesTable"
                >

                    <thead>

                        <tr>

                            <th>
                                Category
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
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
                            $category =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <?php

                            $category_name =
                                $category["name"];

                            $initial =
                                strtoupper(
                                    substr(
                                        $category_name,
                                        0,
                                        1
                                    )
                                );

                            $status =
                                $category["status"]
                                ?? "Active";

                            ?>


                            <tr
                                data-status="<?php
                                echo htmlspecialchars(
                                    $status
                                );
                                ?>"
                            >


                                <!-- CATEGORY -->

                                <td>

                                    <div class="category-cell">


                                        <div
                                            class="category-avatar"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $initial
                                            );
                                            ?>

                                        </div>


                                        <div
                                            class="category-info"
                                        >

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $category_name
                                                );
                                                ?>

                                            </strong>


                                            <span>

                                                Category ID #

                                                <?php
                                                echo $category["id"];
                                                ?>

                                            </span>

                                        </div>

                                    </div>

                                </td>



                                <!-- DESCRIPTION -->

                                <td>

                                    <span
                                        class="category-description"
                                    >

                                        <?php

                                        echo !empty(
                                            $category["description"]
                                        )
                                        ? htmlspecialchars(
                                            $category["description"]
                                        )
                                        : "No description";

                                        ?>

                                    </span>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="category-status <?php
                                        echo strtolower(
                                            $status
                                        );
                                        ?>"
                                    >

                                        <span
                                            class="category-status-dot"
                                        ></span>

                                        <?php
                                        echo htmlspecialchars(
                                            $status
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- CREATED -->

                                <td>

                                    <span
                                        class="category-date"
                                    >

                                        <?php

                                        echo !empty(
                                            $category["created_at"]
                                        )
                                        ? date(
                                            "d M Y",
                                            strtotime(
                                                $category["created_at"]
                                            )
                                        )
                                        : "-";

                                        ?>

                                    </span>

                                </td>



                                <!-- ACTION -->

                                <td>

                                    <a
                                        href="edit-category.php?id=<?php
                                        echo $category["id"];
                                        ?>"
                                        class="category-edit-btn"
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
                                colspan="5"
                                class="category-empty"
                            >

                                <div>

                                    <div
                                        class="category-empty-icon"
                                    >
                                        🏷️
                                    </div>


                                    <h3>
                                        No categories yet
                                    </h3>


                                    <p>
                                        Create your first
                                        category to organize
                                        your products.
                                    </p>


                                    <button
                                        type="button"
                                        class="categories-primary-btn"
                                        onclick="openCategoryModal()"
                                    >

                                        + Add Category

                                    </button>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>



            <!-- FOOTER -->

            <div class="categories-panel-footer">

                <span>

                    Showing

                    <strong id="categoryVisibleCount">

                        <?php
                        echo $total_categories;
                        ?>

                    </strong>

                    categories

                </span>


                <span>
                    Inventory Control System
                </span>

            </div>


        </section>


    </main>

</div>



<!-- =========================================
     ADD CATEGORY MODAL
========================================== -->

<div
    class="category-modal"
    id="categoryModal"
>


    <div class="category-modal-box">


        <div class="category-modal-header">

            <div>

                <h3>
                    Add Category
                </h3>

                <p>
                    Create a new product category.
                </p>

            </div>


            <button
                type="button"
                class="category-modal-close"
                onclick="closeCategoryModal()"
            >
                ×
            </button>

        </div>



        <form
            action="add-category.php"
            method="POST"
        >


            <div class="category-form-group">

                <label>
                    Category Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter category name"
                    required
                >

            </div>



            <div class="category-form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    placeholder="Enter a short description"
                    rows="4"
                ></textarea>

            </div>



            <div class="category-modal-actions">

                <button
                    type="button"
                    class="category-cancel-btn"
                    onclick="closeCategoryModal()"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="category-save-btn"
                >
                    Save Category
                </button>

            </div>


        </form>


    </div>

</div>



<script>

/* =========================================
   SEARCH + FILTER
========================================= */

const categorySearch =
    document.getElementById("categorySearch");

const categoryFilter =
    document.getElementById("categoryFilter");

const categoryRows =
    document.querySelectorAll(
        "#categoriesTable tbody tr[data-status]"
    );

const categoryVisibleCount =
    document.getElementById(
        "categoryVisibleCount"
    );


function filterCategories() {

    const search =
        categorySearch.value
            .toLowerCase()
            .trim();

    const selected =
        categoryFilter.value;

    let count = 0;


    categoryRows.forEach(function(row) {

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


    categoryVisibleCount.innerText =
        count;

}


categorySearch.addEventListener(
    "input",
    filterCategories
);


categoryFilter.addEventListener(
    "change",
    filterCategories
);


/* =========================================
   MODAL
========================================= */

const categoryModal =
    document.getElementById(
        "categoryModal"
    );


function openCategoryModal() {

    categoryModal.classList.add("show");

}


function closeCategoryModal() {

    categoryModal.classList.remove("show");

}


categoryModal.addEventListener(
    "click",
    function(event) {

        if (
            event.target === categoryModal
        ) {

            closeCategoryModal();

        }

    }
);

</script>


</body>

</html>