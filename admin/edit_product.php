<?php
session_start();
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['product_id'])) {
    header('Location: edit_products.php');
    exit;
}

$product_id = intval($_GET['product_id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
$product = mysqli_fetch_assoc($query);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['product_name'];
    $desc = $_POST['product_description'];
    $category = $_POST['product_category'];
    $color = $_POST['product_color'];
    $price = floatval($_POST['product_price']);
    $offer = intval($_POST['product_special_offer']);

    $stmt = $conn->prepare("UPDATE products SET product_name=?, product_description=?, product_category=?, product_color=?, product_price=?, product_special_offer=? WHERE product_id=?");
    $stmt->bind_param('ssssidi', $name, $desc, $category, $color, $price, $offer, $product_id);
    if ($stmt->execute()) {
        header("Location: edit_products.php?updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f2f2f2; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; }
        button { padding: 10px 20px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Product #<?= $product['product_id'] ?></h2>
        <form method="POST">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="product_description" rows="4" required><?= htmlspecialchars($product['product_description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="product_category" value="<?= htmlspecialchars($product['product_category']) ?>" required>
            </div>
            <div class="form-group">
                <label>Color</label>
                <input type="text" name="product_color" value="<?= htmlspecialchars($product['product_color']) ?>" required>
            </div>
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" name="product_price" value="<?= $product['product_price'] ?>" required>
            </div>
            <div class="form-group">
                <label>Special Offer (%)</label>
                <input type="number" name="product_special_offer" min="0" max="100" value="<?= $product['product_special_offer'] ?>">
            </div>
            <button type="submit" name="update_product">Update Product</button>
        </form>
    </div>
</body>
</html>
