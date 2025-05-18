<?php
session_start();
include('../server/connection.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC");

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    mysqli_query($conn, "UPDATE orders SET order_status='$new_status' WHERE order_id=$order_id");
    header("Location: manage_orders.php");
    exit;
}

// Handle delete
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    mysqli_query($conn, "DELETE FROM orders WHERE order_id=$order_id");
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id=$order_id"); // cleanup
    header("Location: manage_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #444; color: white; }
        form { display: inline; }
        a { margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h1>Manage Orders</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Cost</th>
            <th>Status</th>
            <th>City</th>
            <th>Address</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while($order = mysqli_fetch_assoc($orders)): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= $order['user_id'] ?></td>
            <td>$<?= $order['order_cost'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <select name="order_status">
                        <option <?= $order['order_status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option <?= $order['order_status'] == 'Not Paid' ? 'selected' : '' ?>>Not Paid</option>
                        <option <?= $order['order_status'] == 'on_hold' ? 'selected' : '' ?>>on_hold</option>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </td>
            <td><?= $order['user_city'] ?></td>
            <td><?= $order['user_address'] ?></td>
            <td><?= $order['order_date'] ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Delete this order?');">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <button type="submit" name="delete_order">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
