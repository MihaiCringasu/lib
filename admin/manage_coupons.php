<?php
session_start();
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Adaugă cupon nou
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount = intval($_POST['discount_percent']);
    $expires = $_POST['expires_at'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount_percent, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param('sis', $code, $discount, $expires);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_coupons.php?added=1");
    exit;
}

// Șterge cupon
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM coupons WHERE id=$id");
    header("Location: manage_coupons.php?deleted=1");
    exit;
}

$coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Coupons</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
        th { background-color: #333; color: white; }
        .form-inline { margin-top: 30px; background: white; padding: 20px; border-radius: 8px; }
        input, select { padding: 8px; margin: 8px 0; width: 100%; }
        .btn { padding: 8px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-red { background-color: #dc3545; }
        .success { background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 5px; color: #155724; }
    </style>
</head>
<body>
    <h1>Cupon Management</h1>

    <?php if (isset($_GET['added'])): ?><div class="success">Cupon adăugat!</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="success">Cupon șters!</div><?php endif; ?>

    <form method="POST" class="form-inline">
        <h3>Adaugă cupon nou</h3>
        <label>Cod:</label>
        <input type="text" name="code" required>
        <label>Reducere (%):</label>
        <input type="number" name="discount_percent" min="1" max="100" required>
        <label>Data expirare:</label>
        <input type="date" name="expires_at">
        <button type="submit" name="add_coupon" class="btn">Adaugă cupon</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Cod</th>
            <th>Reducere</th>
            <th>Expiră la</th>
            <th>Creat</th>
            <th>Acțiuni</th>
        </tr>
        <?php while ($coupon = mysqli_fetch_assoc($coupons)): ?>
            <tr>
                <td><?= $coupon['id'] ?></td>
                <td><?= htmlspecialchars($coupon['code']) ?></td>
                <td><?= $coupon['discount_percent'] ?>%</td>
                <td><?= $coupon['expires_at'] ?></td>
                <td><?= $coupon['created_at'] ?></td>
                <td><a href="manage_coupons.php?delete=<?= $coupon['id'] ?>" class="btn btn-red" onclick="return confirm('Ștergi acest cupon?')">Șterge</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>