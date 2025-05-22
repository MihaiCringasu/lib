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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Produs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f9f9fb; }
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(106, 13, 173, 0.2);
        }
        .form-header {
            background: #6a0dad;
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 20px;
            text-align: center;
        }
        .btn-success {
            background-color: #6a0dad;
            border-color: #6a0dad;
        }
        .btn-success:hover {
            background-color: #5c0bbb;
            border-color: #5c0bbb;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h3>Adaugă Produs Nou</h3>
        </div>
        <div class="p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nume produs</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categorie</label>
                    <input type="text" name="category" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descriere</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Preț ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reducere (%)</label>
                        <input type="number" name="special_offer" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Culoare</label>
                        <input type="text" name="color" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Imagine <?php echo $i; ?></label>
                        <input type="file" name="image<?php echo $i; ?>" class="form-control" required>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="text-end">
                    <button type="submit" name="add_product" class="btn btn-success">
                        <i class="fas fa-plus"></i> Adaugă Produs
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
