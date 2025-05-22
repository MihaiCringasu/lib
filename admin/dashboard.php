<?php
session_start();
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$admin_email = $_SESSION['admin_email'] ?? 'admin';



// Ștergere cupon
if (isset($_GET['delete_coupon'])) {
    $id = intval($_GET['delete_coupon']);
    mysqli_query($conn, "DELETE FROM coupons WHERE id=$id");
    $success_message = "Cuponul a fost șters.";
}

// Procesare cupon nou cu verificare duplicat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount = intval($_POST['discount_percent']);
    $expires = $_POST['expires_at'];

    $check = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error_message = "Codul '$code' există deja.";
    } else {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_percent, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param('sis', $code, $discount, $expires);
        $stmt->execute();
        $stmt->close();
        $success_message = "Cuponul a fost adăugat cu succes.";
    }
    $check->close();
}

$product_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$user_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$order_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$latest_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
$all_coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: Arial; background: #f4f4f4; }
        .navbar {
            background: #6a0dad; color: #fff; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 3px solid #4e0c9c;
        }
        .navbar h1 { margin: 0; font-size: 24px; display: flex; align-items: center; gap: 10px; }
        .navbar-left { display: flex; align-items: center; gap: 30px; }
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
    <div class="navbar-left">
        <h1><i class="fas fa-user-shield"></i> Admin Panel</h1>
        <div><i class="fas fa-envelope"></i> <?= htmlspecialchars($admin_email) ?></div>
    </div>
    <div>
        <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
        <a href="edit_products.php"><i class="fas fa-edit"></i> Edit Products</a>
        <a href="logout.php" style="background: #444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="card"><h2><i class="fas fa-box"></i> <?= $product_count ?></h2><p>Products</p></div>
        <div class="card"><h2><i class="fas fa-users"></i> <?= $user_count ?></h2><p>Users</p></div>
        <div class="card"><h2><i class="fas fa-shopping-cart"></i> <?= $order_count ?></h2><p>Orders</p></div>
    </div>

    <h2><i class="fas fa-receipt"></i> Recent Orders</h2>
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

    <hr class="my-4">

    <h3><i class="fas fa-tag"></i> Adaugă cupon de reducere</h3>
    <?php if (isset($success_message)): ?><div class="alert alert-success"><?= $success_message ?></div><?php endif; ?>
    <?php if (isset($error_message)): ?><div class="alert alert-danger"><?= $error_message ?></div><?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="code" class="form-control" placeholder="Cod" required>
        </div>
        <div class="col-md-4">
            <input type="number" name="discount_percent" class="form-control" placeholder="Reducere (%)" min="1" max="100" required>
        </div>
        <div class="col-md-4">
            <input type="date" name="expires_at" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" name="add_coupon" class="btn btn-success"><i class="fas fa-plus"></i> Adaugă cupon</button>
        </div>
    </form>

    <h4 class="mt-5"><i class="fas fa-tags"></i> Cuponuri existente</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cod</th>
                <th>Reducere (%)</th>
                <th>Expiră la</th>
                <th>Creat</th>
                <th>Acțiune</th>
            </tr>
        </thead>
        <tbody>
            <?php while($coupon = mysqli_fetch_assoc($all_coupons)): ?>
            <tr>
                <td><?= $coupon['id'] ?></td>
                <td><?= htmlspecialchars($coupon['code']) ?></td>
                <td><?= $coupon['discount_percent'] ?></td>
                <td><?= $coupon['expires_at'] ?></td>
                <td><?= $coupon['created_at'] ?></td>
                <td>
                    <a href="dashboard.php?delete_coupon=<?= $coupon['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ștergi acest cupon?')">
                        <i class="fas fa-trash-alt"></i> Șterge
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>