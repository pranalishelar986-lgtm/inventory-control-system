<?php
// db.php - Shared connection for both Admin and Client panels
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>