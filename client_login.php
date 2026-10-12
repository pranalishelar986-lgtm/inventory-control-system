<?php
session_start();
include 'db.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM customers WHERE email = '$email'");
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_name'] = $user['name'];
            header("Location: client_dashboard.php");
            exit();
        } else {
            $msg = "Invalid password!";
        }
    } else {
        $msg = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login - Inventory System</title>
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
        <h2>Client Login</h2>
        <?php if($msg): ?><p style="color:red; margin-bottom:10px;"><?php echo $msg; ?></p><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="client_register.php">Register here</a></p>
    </div>
</body>
</html>