<?php
session_start();
include('../server/connection.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products");

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);
    mysqli_query($conn, "DELETE FROM products WHERE product_id=$product_id");
    header("Location: edit_products.php?deleted=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Products</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; vertical-align: middle; }
        th { background-color: #333; color: white; }
        .img-name-wrapper { display: flex; align-items: center; gap: 12px; justify-content: start; }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        a.button, button { padding: 6px 12px; margin: 2px; font-size: 14px; border: none; cursor: pointer; }
        a.button { text-decoration: none; color: white; background: #007BFF; border-radius: 4px; display: inline-block; }
        .btn-green { background-color: #28a745; }
        .btn-red { background-color: #dc3545; color: white; }
        form { display: inline; }
        .success { background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #155724; }
    </style>
</head>
<body>
    <h1>Edit Products</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success">Product deleted successfully!</div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Category</th>
            <th>Color</th>
            <th>Price</th>
            <th>Offer (%)</th>
            <th>Actions</th>
        </tr>
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
        <tr>
            <td><?= $product['product_id'] ?></td>
            <td>
                <div class="img-name-wrapper">
                    <img src="../assets/img/<?= htmlspecialchars($product['product_image']) ?>" alt="Image" class="product-thumb">
                    <?= htmlspecialchars($product['product_name']) ?>
                </div>
            </td>
            <td><?= htmlspecialchars($product['product_category']) ?></td>
            <td><?= htmlspecialchars($product['product_color']) ?></td>
            <td>$<?= $product['product_price'] ?></td>
            <td><?= $product['product_special_offer'] ?>%</td>
            <td>
                <a href="edit_product.php?product_id=<?= $product['product_id'] ?>" class="button">Edit</a>
                <a href="edit_images.php?product_id=<?= $product['product_id'] ?>" class="button btn-green">Edit Images</a>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <button type="submit" name="delete_product" class="btn-red">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
