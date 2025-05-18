<?php

session_start();
include('../server/connection.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$product_id = intval($_GET['product_id']);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id=$product_id"));

if (!$product) {
    die("Product not found.");
}

// Handle image updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 1; $i <= 4; $i++) {
        $field = "product_image" . ($i === 1 ? "" : $i); // first image: product_image, rest: product_image2, 3, 4
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES[$field]['tmp_name'];
            $file_name = basename($_FILES[$field]['name']);
            $target_path = "assets/img/" . $file_name;
            move_uploaded_file($tmp_name, $target_path);
            mysqli_query($conn, "UPDATE products SET $field='$file_name' WHERE product_id=$product_id");
        }
    }
    header("Location: edit_images.php?product_id=$product_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product Images</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; }
        h1 { margin-bottom: 20px; }
        .image-block { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .image-block img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc; }
        .image-block input[type="file"] { display: block; }
        button { padding: 10px 20px; font-size: 16px; margin-top: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        a { display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="edit_products.php">← Back to Products</a>
        <h1>Edit Images: <?= htmlspecialchars($product['product_name']) ?></h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="success">Images updated successfully!</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php
            $image_fields = [
                'product_image' => 'Main Image',
                'product_image2' => 'Image 2',
                'product_image3' => 'Image 3',
                'product_image4' => 'Image 4',
            ];

            foreach ($image_fields as $field => $label):
                $image_file = $product[$field];
            ?>
            <div class="image-block">
                <img src="assets/img/<?= htmlspecialchars($image_file) ?>" alt="<?= $label ?>">
                <div>
                    <label for="<?= $field ?>">Replace <?= $label ?>:</label>
                    <input type="file" name="<?= $field ?>" accept="image/*">
                </div>
            </div>
            <?php endforeach; ?>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>