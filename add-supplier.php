<?php
// add-supplier.php

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

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name  = isset($_POST['supplier_name']) ? $conn->real_escape_string($_POST['supplier_name']) : '';
    $contact_person = isset($_POST['contact_person']) ? $conn->real_escape_string($_POST['contact_person']) : '';
    $email          = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $address        = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
    $status         = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'Active';

    if (!empty($supplier_name)) {
        // List of column variations for name and contact person
        $queries = [
            "INSERT INTO suppliers (supplier_name, contact_person, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')",
            "INSERT INTO suppliers (name, contact_person, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')",
            "INSERT INTO suppliers (supplier_name, contact, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')",
            "INSERT INTO suppliers (name, contact, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')",
            "INSERT INTO suppliers (supplier_name, phone, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')",
            "INSERT INTO suppliers (name, phone, email, address, status) VALUES ('$supplier_name', '$contact_person', '$email', '$address', '$status')"
        ];

        $inserted = false;
        foreach ($queries as $sql) {
            if ($conn->query($sql) === TRUE) {
                $inserted = true;
                break;
            }
        }

        if ($inserted) {
            $message = "Supplier added successfully! <a href='suppliers.php'>Back to Suppliers</a>";
        } else {
            $message = "Error adding supplier: " . $conn->error;
        }
    } else {
        $message = "Please enter a supplier name.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
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
    <h2>Add New Supplier</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="supplier_name" placeholder="e.g. TechSource Logistics" required>
        </div>

        <div class="form-group">
            <label>Contact Person / Phone</label>
            <input type="text" name="contact_person" placeholder="e.g. Sarah Jenkins">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="e.g. contact@techsource.com">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" placeholder="e.g. 101 Innovation Way, San Jose, CA"></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Save Supplier</button>
    </form>

    <a href="suppliers.php" class="btn-back">&larr; Back to Suppliers</a>
</div>

</body>
</html>