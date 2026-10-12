<?php
// edit-category.php

// 1. Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$category = null;

// 2. Fetch Existing Category Data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM categories WHERE id = $id");
    
    if ($result && $result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        die("Category not found.");
    }
} else {
    header("Location: categories.php");
    exit();
}

// 3. Handle Form Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = isset($_POST['category_name']) ? $conn->real_escape_string($_POST['category_name']) : (isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '');
    $description   = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $status        = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'Active';

    // Update query targeting category_name column
    $sql = "UPDATE categories SET category_name='$category_name', description='$description', status='$status' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "Category updated successfully! <a href='categories.php'>Back to Categories</a>";
        // Refresh category data
        $result = $conn->query("SELECT * FROM categories WHERE id = $id");
        $category = $result->fetch_assoc();
    } else {
        // Fallback update if column name is 'name' instead of 'category_name'
        $fallback_sql = "UPDATE categories SET name='$category_name', description='$description', status='$status' WHERE id=$id";
        if ($conn->query($fallback_sql) === TRUE) {
            $message = "Category updated successfully! <a href='categories.php'>Back to Categories</a>";
            $result = $conn->query("SELECT * FROM categories WHERE id = $id");
            $category = $result->fetch_assoc();
        } else {
            $message = "Error updating category: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #1e293b; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #334155; font-size: 14px; }
        input[type="text"], textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; outline: none; }
        textarea { height: 80px; resize: vertical; }
        input:focus, textarea:focus, select:focus { border-color: #2563eb; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 12px 15px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 15px; font-weight: 600; margin-top: 10px; }
        .btn-submit:hover { background-color: #1d4ed8; }
        .btn-back { display: inline-block; margin-top: 18px; text-decoration: none; color: #64748b; font-size: 14px; }
        .btn-back:hover { color: #334155; }
        .alert { padding: 12px; background-color: #d1e7dd; color: #0f5132; margin-bottom: 20px; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Category</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" name="category_name" value="<?php echo htmlspecialchars($category['category_name'] ?? $category['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="Active" <?php echo (($category['status'] ?? '') == 'Active') ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo (($category['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Update Category</button>
    </form>

    <a href="categories.php" class="btn-back">&larr; Back to Categories</a>
</div>

</body>
</html>