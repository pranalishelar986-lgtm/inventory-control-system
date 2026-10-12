<?php
// edit-product.php

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Fetch Product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

if ($product_id <= 0) {
    die("Invalid product ID.");
}

// 3. Handle Form Submission (Update Product)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $sku          = $conn->real_escape_string($_POST['sku']);
    $category     = $conn->real_escape_string($_POST['category']);
    $price        = floatval($_POST['price']);
    $stock        = intval($_POST['stock']);

    // Check existing columns dynamically or update standard fields
    $update_sql = "UPDATE products SET 
                    product_name = '$product_name', 
                    sku = '$sku', 
                    category = '$category', 
                    price = '$price', 
                    stock = '$stock' 
                   WHERE id = $product_id";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Product updated successfully! <a href='products.php'>Go back to Products</a>";
    } else {
        // Fallback query if column names are 'name' and 'quantity'
        $fallback_sql = "UPDATE products SET 
                          name = '$product_name', 
                          sku = '$sku', 
                          category = '$category', 
                          price = '$price', 
                          quantity = '$stock' 
                         WHERE id = $product_id";
        
        if ($conn->query($fallback_sql) === TRUE) {
            $message = "Product updated successfully! <a href='products.php'>Go back to Products</a>";
        } else {
            $message = "Error updating product: " . $conn->error;
        }
    }
}

// 4. Fetch Current Product Details
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// Extract field values safely without throwing undefined key warnings
$val_name     = $product['product_name'] ?? $product['name'] ?? $product['title'] ?? '';
$val_sku      = $product['sku'] ?? $product['code'] ?? '';
$val_category = $product['category'] ?? $product['category_name'] ?? '';
$val_price    = $product['price'] ?? 0;
$val_stock    = $product['stock'] ?? $product['quantity'] ?? $product['qty'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #1e293b; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #334155; font-size: 14px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; outline: none; }
        input[type="text"]:focus, input[type="number"]:focus { border-color: #2563eb; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 12px 15px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 15px; font-weight: 600; margin-top: 10px; }
        .btn-submit:hover { background-color: #1d4ed8; }
        .btn-back { display: inline-block; margin-top: 18px; text-decoration: none; color: #64748b; font-size: 14px; }
        .btn-back:hover { color: #334155; }
        .alert { padding: 12px; background-color: #d1e7dd; color: #0f5132; margin-bottom: 20px; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($val_name); ?>" required>
        </div>

        <div class="form-group">
            <label>SKU</label>
            <input type="text" name="sku" value="<?php echo htmlspecialchars($val_sku); ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($val_category); ?>" required>
        </div>

        <div class="form-group">
            <label>Price (₹)</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($val_price); ?>" required>
        </div>

        <div class="form-group">
            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($val_stock); ?>" required>
        </div>

        <button type="submit" class="btn-submit">Update Product</button>
    </form>

    <a href="products.php" class="btn-back">&larr; Back to Products</a>
</div>

</body>
</html>