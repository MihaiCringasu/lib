<?php
session_start();
include('server/connection.php');

// Adaugare produs
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['cart'])) {
        $products_array_ids = array_column($_SESSION['cart'], "product_id");
        if (!in_array($_POST['product_id'], $products_array_ids)) {
            $product_id = $_POST['product_id'];
            $product_array = array(
                'product_id' => $_POST['product_id'],
                'product_name' => $_POST['product_name'],
                'product_price' => $_POST['product_price'],
                'product_image' => $_POST['product_image'],
                'product_quantity' => $_POST['product_quantity'],
            );
            $_SESSION['cart'][$product_id] = $product_array;
        } else {
            $product_id = $_POST['product_id'];
            $_SESSION['cart'][$product_id]['product_quantity'] += $_POST['product_quantity'];
        }
    } else {
        $product_array = array(
            'product_id' => $_POST['product_id'],
            'product_name' => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'product_image' => $_POST['product_image'],
            'product_quantity' => $_POST['product_quantity']
        );
        $_SESSION['cart'][$_POST['product_id']] = $product_array;
    }
    calculateTotalCart();
    header("Location: cart.php?added=1");
    exit;
}

elseif (isset($_POST['remove_product'])) {
    unset($_SESSION['cart'][$_POST['product_id']]);
    calculateTotalCart();
}

elseif (isset($_POST['edit_quantity'])) {
    $_SESSION['cart'][$_POST['product_id']]['product_quantity'] = $_POST['product_quantity'];
    calculateTotalCart();
}

elseif (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    unset($_SESSION['total']);
    unset($_SESSION['quantity']);
    unset($_SESSION['discount_applied']);
    unset($_SESSION['discount_code']);
    unset($_SESSION['discount_percent']);
}

function calculateTotalCart()
{
    $total = 0;
    $total_quantity = 0;
    foreach ($_SESSION['cart'] as $product) {
        $total += $product['product_price'] * $product['product_quantity'];
        $total_quantity += $product['product_quantity'];
    }

    if (isset($_SESSION['discount_applied']) && isset($_SESSION['discount_percent'])) {
        $discount = $_SESSION['discount_percent'];
        $total = $total * ((100 - $discount) / 100);
    }

    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $total_quantity;
}
?>

<?php include('layouts/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<section class="container my-5 py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i> Coșul tău</h2>
        <hr class="w-25 mx-auto">
    </div>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="table-responsive">
            <form method="POST" action="cart.php">
                <button name="clear_cart" class="btn btn-danger btn-sm mb-3"><i class="fas fa-trash"></i> Golește Coșul</button>
            </form>
            <table class="table align-middle table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produs</th>
                        <th>Cantitate</th>
                        <th>Subtotal</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $product): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="assets/img/<?php echo $product['product_image']; ?>" width="60" class="me-3 rounded shadow-sm" alt="">
                                    <div>
                                        <h6 class="mb-0 text-dark"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                        <small class="text-muted">$<?php echo $product['product_price']; ?> / buc</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="cart.php" class="d-flex">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="number" class="form-control me-2" name="product_quantity" value="<?php echo $product['product_quantity']; ?>" min="1">
                                    <button type="submit" name="edit_quantity" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="fw-bold text-success">
                                $<?php echo number_format($product['product_price'] * $product['product_quantity'], 2); ?>
                            </td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" name="remove_product" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Șterge
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form id="discount-form" class="my-3 d-flex">
            <input type="text" name="discount_code" class="form-control me-2" placeholder="Cod reducere" required>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-percent"></i> Aplică reducere
            </button>
        </form>

        <div id="discount-message" class="mt-2"></div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <h4>Total: <span class="text-primary">$<?php echo number_format($_SESSION['total'], 2); ?></span></h4>
            <form method="POST" action="checkout.php">
                <button type="submit" class="btn btn-success px-4" name="checkout">
                    <i class="fas fa-credit-card me-2"></i> Mergi la Checkout
                </button>
            </form>
      </div>

    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i> Coșul este gol.
        </div>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('discount-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('apply_coupon.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const msgBox = document.getElementById('discount-message');
            msgBox.textContent = data.message;
            msgBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';
            if (data.success) {
                setTimeout(() => window.location.reload(), 1000);
            }
        });
    });
</script>
<script>
    document.getElementById('discount-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('apply_coupon.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                const msgBox = document.getElementById('discount-message');
                msgBox.textContent = data.message;
                msgBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';

                // Dacă e cu succes, actualizează totalul și afișează temporar mesajul
                if (data.success) {
                    setTimeout(() => {
                        msgBox.style.display = 'none';
                        window.location.reload(); // forțează recalcule din PHP
                    }, 3000);
                } else {
                    setTimeout(() => {
                        msgBox.style.display = 'none';
                    }, 3000);
                }
            });
    });
</script>       


<?php include('layouts/footer.php'); ?>
</body>
</html>