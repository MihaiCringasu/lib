<?php
session_start();
include('../server/connection.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $special_offer = $_POST['special_offer'];
    $color = $_POST['color'];

    // Upload Images
    $image_names = [];
    for ($i = 1; $i <= 4; $i++) {
        $img = $_FILES["image$i"];
        $img_name = time() . basename($img["name"]);
        $target_file = "../assets/img/" . $img_name;
        move_uploaded_file($img["tmp_name"], $target_file);
        $image_names[] = $img_name;
    }

    $stmt = $conn->prepare("INSERT INTO products (product_name, product_category, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_special_offer, product_color) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdis", $name, $category, $description, $image_names[0], $image_names[1], $image_names[2], $image_names[3], $price, $special_offer, $color);

    if ($stmt->execute()) {
        header('Location: dashboard.php?message=Product added successfully');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css"> <!-- Customize as needed -->
</head>

<body>
    <h2>Add New Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Special Offer (%):</label><br>
        <input type="number" name="special_offer" required><br><br>

        <label>Color:</label><br>
        <input type="text" name="color" required><br><br>

        <label>Image 1:</label><br>
        <input type="file" name="image1" required><br><br>

        <label>Image 2:</label><br>
        <input type="file" name="image2" required><br><br>

        <label>Image 3:</label><br>
        <input type="file" name="image3" required><br><br>

        <label>Image 4:</label><br>
        <input type="file" name="image4" required><br><br>

        <input type="submit" name="add_product" value="Add Product">
    </form>
</body>
</html>
