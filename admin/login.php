<?php
session_start();
include('../server/connection.php');


$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && $password === $admin['admin_password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $admin['admin_name'];
        $_SESSION['admin_id'] = $admin['admin_id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #6a0dad;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            margin-top: 20px;
            background: #6a0dad;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .error {
            margin-top: 10px;
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="admin_email" required>

            <label>Password</label>
            <input type="password" name="admin_password" required>

            <button type="submit">Login</button>

            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
