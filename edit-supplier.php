<?php
// edit-supplier.php

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
$supplier = null;

// 2. Fetch Existing Supplier Data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM suppliers WHERE id = $id");
    
    if ($result && $result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
    } else {
        die("Supplier not found.");
    }
} else {
    header("Location: suppliers.php");
    exit();
}

// 3. Handle Form Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name  = isset($_POST['supplier_name']) ? $conn->real_escape_string($_POST['supplier_name']) : '';
    $contact_person = isset($_POST['contact_person']) ? $conn->real_escape_string($_POST['contact_person']) : '';
    $email          = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $address        = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
    $status         = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'Active';

    // List of queries to match your database column names automatically
    $queries = [
        "UPDATE suppliers SET supplier_name='$supplier_name', contact_person='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id",
        "UPDATE suppliers SET name='$supplier_name', contact_person='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id",
        "UPDATE suppliers SET supplier_name='$supplier_name', contact='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id",
        "UPDATE suppliers SET name='$supplier_name', contact='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id",
        "UPDATE suppliers SET supplier_name='$supplier_name', phone='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id",
        "UPDATE suppliers SET name='$supplier_name', phone='$contact_person', email='$email', address='$address', status='$status' WHERE id=$id"
    ];

    $updated = false;
    foreach ($queries as $sql) {
        if ($conn->query($sql) === TRUE) {
            $updated = true;
            break;
        }
    }

    if ($updated) {
        $message = "Supplier updated successfully! <a href='suppliers.php'>Back to Suppliers</a>";
        // Refresh supplier data
        $res = $conn->query("SELECT * FROM suppliers WHERE id = $id");
        $supplier = $res->fetch_assoc();
    } else {
        $message = "Error updating supplier: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #1e293b; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #334155; font-size: 14px; }
        input[type="text"], input[type="email"], textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; outline: none; }
        textarea { height: 70px; resize: vertical; }
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
    <h2>Edit Supplier</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name'] ?? $supplier['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Person / Phone</label>
            <input type="text" name="contact_person" value="<?php echo htmlspecialchars($supplier['contact_person'] ?? $supplier['contact'] ?? $supplier['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($supplier['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="Active" <?php echo (($supplier['status'] ?? '') == 'Active') ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo (($supplier['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Update Supplier</button>
    </form>

    <a href="suppliers.php" class="btn-back">&larr; Back to Suppliers</a>
</div>

</body>
</html>