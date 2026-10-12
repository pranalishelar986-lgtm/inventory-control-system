<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Inventory Control System</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #f8fafc;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background: #ffffff;
      width: 100%;
      max-width: 420px;
      padding: 40px 32px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    .brand-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .brand-icon {
      width: 48px;
      height: 48px;
      background: #2563eb;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin: 0 auto 12px auto;
      box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .brand-header h1 {
      font-size: 1.35rem;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .brand-header p {
      color: #64748b;
      font-size: 0.875rem;
      margin-top: 4px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 500;
      color: #334155;
      margin-bottom: 6px;
    }

    .form-group input {
      width: 100%;
      padding: 10px 14px;
      font-size: 0.875rem;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      outline: none;
      transition: all 0.15s ease-in-out;
    }

    .form-group input:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn-login {
      width: 100%;
      background: #2563eb;
      color: #ffffff;
      font-weight: 600;
      font-size: 0.875rem;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.15s ease-in-out;
      margin-top: 8px;
    }

    .btn-login:hover {
      background: #1d4ed8;
    }

    .demo-credentials {
      margin-top: 24px;
      padding: 12px;
      background: #f1f5f9;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      font-size: 0.8rem;
      color: #475569;
      text-align: center;
    }

    .demo-credentials strong {
      color: #0f172a;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <div class="brand-header">
      <div class="brand-icon">📦</div>
      <h1>Inventory Control System</h1>
      <p>Sign in to access your dashboard</p>
    </div>

    <form action="dashboard.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" value="admin" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="btn-login">Sign In</button>
    </form>

    <div class="demo-credentials">
      Demo Login: <strong>admin</strong> / <strong>admin123</strong>
    </div>
  </div>

</body>
</html>