<?php
include 'db.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);

    $check = $conn->query("SELECT * FROM customers WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $msg = "Email already registered!";
    } else {
        $sql = "INSERT INTO customers (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";
        if ($conn->query($sql) === TRUE) {
            header("Location: client_login.php");
            exit();
        } else {
            $msg = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Register - Inventory System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 350px; }
        h2 { margin-bottom: 20px; color: #0f172a; font-size: 22px; }
        input { width: 100%; padding: 10px 14px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        button { width: 100%; padding: 11px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        p { margin-top: 15px; font-size: 13px; text-align: center; color: #64748b; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Client Register</h2>
        <?php if($msg): ?><p style="color:red; margin-bottom:10px;"><?php echo $msg; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone Number">
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="client_login.php">Login here</a></p>
    </div>
</body>
</html>