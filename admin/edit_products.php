<?php
session_start();
include('../server/connection.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products");

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body { background: #f4f4f4; font-family: Arial; }
        .container { max-width: 1200px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(106, 13, 173, 0.2);}
        h1 { background: #6a0dad; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; margin: -30px -30px 30px -30px; }
        .search-bar { margin-bottom: 20px; text-align: right; }
        .search-bar input { max-width: 300px; display: inline-block; }
        table {  width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; vertical-align: middle; }
        th { background-color: #6a0dad; color: white; }
        .img-name-wrapper { display: flex; align-items: center; gap: 12px; justify-content: start; }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .action-buttons { display: flex; gap: 6px; justify-content: center; flex-wrap: nowrap; }
        a.button, button { padding: 6px 12px; font-size: 14px; border: none; cursor: pointer; border-radius: 4px; }
        a.button { text-decoration: none; color: white; background: #6a0dad; }
        .btn-green { background-color: #4caf50; color: white; }
        .btn-red { background-color: #dc3545; color: white; }
        form { display: inline; }
        .success { background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: #155724; }
    </style>
    <script>
        function filterProducts() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Edit Products</h1>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="success">Product deleted successfully!</div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" onkeyup="filterProducts()" class="form-control" placeholder="Caută produs...">
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Color</th>
                    <th>Price</th>
                    <th>Offer (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
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
                        <div class="action-buttons">
                            <a href="edit_product.php?product_id=<?= $product['product_id'] ?>" class="button">Edit</a>
                            <a href="edit_images.php?product_id=<?= $product['product_id'] ?>" class="button btn-green">Images</a>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                <button type="submit" name="delete_product" class="btn-red">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>
