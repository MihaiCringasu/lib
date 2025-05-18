<?php
session_start();
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$product_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$user_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$order_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$latest_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { margin: 0; padding: 0; font-family: Arial; background: #f4f4f4; }
        .navbar {
            background: #6a0dad; color: #fff; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 3px solid #4e0c9c;
        }
        .navbar h1 { margin: 0; font-size: 24px; }
        .navbar a {
            color: #fff; margin-left: 20px; text-decoration: none; font-weight: bold;
            padding: 6px 10px; border-radius: 6px; transition: opacity 0.2s;
        }
        .navbar a:hover { opacity: 0.8; }
        .container {
            max-width: 1000px; margin: 30px auto; background: #fff;
            padding: 20px; border-radius: 8px;
        }
        .stats { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .card {
            flex: 1; margin: 0 10px; background: #222; color: #fff;
            padding: 20px; border-radius: 8px; text-align: center;
        }
        table {
            width: 100%; border-collapse: collapse; margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd; padding: 10px; text-align: center;
        }
        th { background: #333; color: #fff; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Admin Panel</h1>
        <div>
            <a href="add_product.php">Add Product</a>
            <a href="edit_products.php">Edit Products</a>
            <a href="logout.php" style="background: #444;">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="stats">
            <div class="card"><h2><?= $product_count ?></h2><p>Products</p></div>
            <div class="card"><h2><?= $user_count ?></h2><p>Users</p></div>
            <div class="card"><h2><?= $order_count ?></h2><p>Orders</p></div>
        </div>

        <h2>Recent Orders</h2>
        <table>
            <tr>
                <th>Order ID</th><th>User ID</th><th>Cost</th>
                <th>Status</th><th>City</th><th>Date</th>
            </tr>
            <?php while($order = mysqli_fetch_assoc($latest_orders)): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['user_id'] ?></td>
                <td>$<?= $order['order_cost'] ?></td>
                <td><?= $order['order_status'] ?></td>
                <td><?= $order['user_city'] ?></td>
                <td><?= $order['order_date'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
