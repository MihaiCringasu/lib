<?php
session_start();
include('../server/connection.php');
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

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

// Trimite newsletter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_newsletter'])) {
    $subject = trim($_POST['newsletter_subject']);
    $message = trim($_POST['newsletter_message']);

    $subscribers = mysqli_query($conn, "SELECT email FROM subscribers");
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';
    require '../PHPMailer/src/Exception.php';


    while ($row = mysqli_fetch_assoc($subscribers)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'infocompanyemail2@gmail.com';
            $mail->Password = 'kfya epik efyv ncjq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('infocompanyemail2@gmail.com', 'Charm');
            $mail->addAddress($row['email']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($message);

            $mail->send();
        } catch (Exception $e) {
            // Log or handle error
        }
    }
    $success_message = "Newsletter trimis către toți abonații.";
}

$product_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$user_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$order_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$latest_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
$all_coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY created_at DESC");
$categories = mysqli_query($conn, "SELECT DISTINCT product_category FROM products");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;
$total_orders = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_pages = ceil($total_orders / $per_page);
$lates
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
        .category-box {
            background: #eee;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            text-transform: capitalize;
            color: #6a0dad;
        }
        .modal textarea {
            resize: vertical;
        }
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
        <a href="#" data-bs-toggle="modal" data-bs-target="#newsletterModal"><i class="fas fa-paper-plane"></i> Newsletter</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#couponsModal"><i class="fas fa-tag"></i> Cupoane</a>
        <a href="logout.php" style="background: #444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="card"><h2><i class="fas fa-box"></i> <?= $product_count ?></h2><p>Products</p></div>
        <div class="card"><h2><i class="fas fa-users"></i> <?= $user_count ?></h2><p>Users</p></div>
        <div class="card"><h2><i class="fas fa-shopping-cart"></i> <?= $order_count ?></h2><p>Orders</p></div>
    </div>

    <h3 class="mt-5"><i class="fas fa-receipt"></i> Comenzi recente</h3>
    <table>
        <tr>
            <th>ID</th><th>User ID</th><th>Cost</th><th>Status</th><th>Oraș</th><th>Data</th>
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
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Modal Newsletter -->
<div class="modal fade" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-paper-plane"></i> Trimite Newsletter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Închide"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="newsletter_subject" class="form-control mb-3" placeholder="Subiect" required>
          <textarea name="newsletter_message" class="form-control" rows="6" placeholder="Mesaj" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" name="send_newsletter" class="btn btn-primary">Trimite</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cupoane -->
<div class="modal fade" id="couponsModal" tabindex="-1" aria-labelledby="couponsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-tags"></i> Cuponuri Reducere</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Închide"></button>
      </div>
      <div class="modal-body">
        <form method="POST" class="row g-3 mb-4">
          <div class="col-md-4">
            <input type="text" name="code" class="form-control" placeholder="Cod cupon" required>
          </div>
          <div class="col-md-4">
            <input type="number" name="discount_percent" class="form-control" placeholder="Reducere (%)" required>
          </div>
          <div class="col-md-4">
            <input type="date" name="expires_at" class="form-control">
          </div>
          <div class="col-12 text-end">
            <button type="submit" name="add_coupon" class="btn btn-success"><i class="fas fa-plus"></i> Adaugă cupon</button>
          </div>
        </form>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th><th>Cod</th><th>Reducere</th><th>Expiră</th><th>Creat</th><th>Acțiune</th>
            </tr>
          </thead>
          <tbody>
            <?php while($coupon = mysqli_fetch_assoc($all_coupons)): ?>
              <tr>
                <td><?= $coupon['id'] ?></td>
                <td><?= htmlspecialchars($coupon['code']) ?></td>
                <td><?= $coupon['discount_percent'] ?>%</td>
                <td><?= $coupon['expires_at'] ?></td>
                <td><?= $coupon['created_at'] ?></td>
                <td>
                  <a href="dashboard.php?delete_coupon=<?= $coupon['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ștergi acest cupon?')">
                    Șterge
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
