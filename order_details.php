<?php
include('server/connection.php');
session_start();

if (!isset($_POST['order_details_btn']) || !isset($_POST['order_id'])) {
    header('location: account.php');
    exit;
}

$order_id = $_POST['order_id'];
$order_status = $_POST['order_status'];

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_details = $stmt->get_result();

// Fetch extra metadata
$meta_query = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id");
$meta = mysqli_fetch_assoc($meta_query);
$order_total_price = $meta['order_cost'];
?>

<?php include('layouts/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-5">
        <h2 class="fw-bold text-center"><i class="fas fa-receipt me-2 text-success"></i>Detalii Comandă</h2>
        <hr class="mx-auto w-25">
    </div>

    <table class="table table-bordered text-center">
        <thead class="table-light">
            <tr>
                <th>Produs</th>
                <th>Preț</th>
                <th>Cantitate</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_details as $row): ?>
                <tr>
                    <td class="text-start">
                        <div class="d-flex align-items-center">
                            <img src="assets/img/<?php echo $row['product_image']; ?>" width="60" class="me-3 rounded shadow-sm">
                            <span><?php echo $row['product_name']; ?></span>
                        </div>
                    </td>
                    <td>$<?php echo $row['product_price']; ?></td>
                    <td><?php echo $row['product_quantity']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="card p-4 mt-4 shadow-sm">
        <h5><i class="fas fa-info-circle me-2 text-primary"></i>Informații suplimentare:</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <i class="fas fa-barcode me-2"></i>ID comandă: <strong>#<?php echo $order_id; ?></strong>
            </li>
            <li class="list-group-item">
                <i class="fas fa-calendar-alt me-2"></i>Data: <strong><?php echo $meta['order_date']; ?></strong>
            </li>
            <li class="list-group-item">
                <i class="fas fa-money-bill-wave me-2"></i>Total: <strong>$<?php echo number_format($order_total_price, 2); ?></strong>
            </li>
            <li class="list-group-item">
                <i class="fas fa-percentage me-2"></i>Reducere: <strong>
                <?php echo !empty($meta['coupon_code']) ? $meta['coupon_code'] . " ({$meta['coupon_discount']}%)" : 'Niciuna'; ?>
                </strong>
            </li>
            <li class="list-group-item">
                <i class="fas fa-credit-card me-2"></i>Status plată: <strong><?php echo ucfirst($order_status); ?></strong>
            </li>
        </ul>

        <?php if ($order_status == "not paid"): ?>
            <form class="text-end mt-3" method="POST" action="payment.php">
                <input type="hidden" name="order_total_price" value="<?php echo $order_total_price; ?>" />
                <input type="hidden" name="order_status" value="<?php echo $order_status; ?>" />
                <button type="submit" name="order_pay_btn" class="btn btn-primary">
                    <i class="fas fa-credit-card me-1"></i> Plătește acum
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="alert alert-success mt-4 text-center">
        <i class="fas fa-check-circle me-2"></i> 
        <strong>Mulțumim pentru comanda ta!</strong> Ne bucurăm să te avem ca client.
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<?php include('layouts/footer.php'); ?>
</body>
</html>