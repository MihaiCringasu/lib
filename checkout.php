<?php
session_start();
include('server/connection.php');

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('location: index.php');
    exit;
}

// Calculează total din cart (în loc de $_SESSION['total'] hardcodat)

// Calculează totalul din nou doar dacă nu e deja în sesiune
$total = 0;
$total_quantity = 0;
foreach ($_SESSION['cart'] as $product) {
    $total += $product['product_price'] * $product['product_quantity'];
    $total_quantity += $product['product_quantity'];
}

// Aplică reducerea dacă este cupon
if (isset($_SESSION['discount_applied']) && isset($_SESSION['discount_percent'])) {
    $discount = $_SESSION['discount_percent'];
    $total = $total * ((100 - $discount) / 100);
}

$_SESSION['total'] = $total;
$_SESSION['quantity'] = $total_quantity;


// Stripe
require 'vendor/autoload.php';
$stripeConfig = include('stripe.php');
\Stripe\Stripe::setApiKey($stripeConfig['secret_key']);

// Checkout submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $_SESSION['checkout'] = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'city' => $_POST['city'],
        'address' => $_POST['address']
    ];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_cost, order_status, order_date) VALUES (?, ?, 'Not Paid', NOW())");
    $stmt->bind_param('id', $_SESSION['user_id'], $_SESSION['total']);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Stripe checkout session
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Your Cart Total',
                ],
                'unit_amount' => $_SESSION['total'] * 100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'customer_email' => $_POST['email'],
        'metadata' => [
            'order_id' => $order_id, 
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'city' => $_POST['city'],
            'address' => $_POST['address'],
        ],
        'success_url' => 'http://localhost/licenta2/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/licenta2/cancel.php',
    ]);

    header("Location: " . $session->url);
    exit;
}
?>

<?php include('layouts/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<section class="my-5 py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Checkout</h2>
            <hr class="w-25 mx-auto">
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-warning text-center">
                <?php echo $_GET['message']; ?>
                <a href="login.php" class="btn btn-sm btn-primary ms-2">Login</a>
            </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <!-- Mini Cart Summary -->
            <div class="col-lg-5">
                <div class="card shadow-sm p-4 border-0">
                    <h5 class="mb-4"><i class="fas fa-shopping-cart text-success me-2"></i>Produse în coș</h5>
                    <?php foreach ($_SESSION['cart'] as $product): ?>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                <small class="text-muted">Cantitate: <?php echo $product['product_quantity']; ?></small>
                            </div>
                            <span class="fw-bold text-success">
                                $<?php echo number_format($product['product_price'] * $product['product_quantity'], 2); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>


                    <?php if (isset($_SESSION['discount_applied']) && isset($_SESSION['discount_percent'])): ?>
                        <?php
                            // Calculează totalul fără reducere
                            $total_without_discount = 0;
                            foreach ($_SESSION['cart'] as $product) {
                                $total_without_discount += $product['product_price'] * $product['product_quantity'];
                            }
                            $discount_value = $total_without_discount - $_SESSION['total'];
                        ?>
                        <div class="d-flex justify-content-between border-top pt-3 mt-3">
                            <h6 class="text-muted">Reducere aplicată (<?php echo $_SESSION['discount_percent']; ?>%):</h6>
                            <h6 class="text-danger">- $<?php echo number_format($discount_value, 2); ?></h6>
                        </div>
                    <?php endif; ?>
                    

                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                        <h5>Total:</h5>
                        <h5 class="text-primary">$<?php echo number_format($_SESSION['total'], 2); ?></h5>
                    </div>
                </div>
            </div>


            <!-- Checkout Form -->
            <div class="col-lg-7">
                <div class="card shadow-sm p-4 border-0">
                    <form id="checkout-form" method="POST">
                        <div class="form-group mb-3">
                            <label for="name"><i class="fas fa-user me-1"></i> Nume complet</label>
                            <input type="text" class="form-control" name="name" placeholder="Ex: Popescu Andrei" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email"><i class="fas fa-envelope me-1"></i> Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Ex: email@example.com" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone"><i class="fas fa-phone me-1"></i> Telefon</label>
                            <input type="tel" class="form-control" name="phone" placeholder="07xx xxx xxx" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="city"><i class="fas fa-city me-1"></i> Oraș</label>
                            <input type="text" class="form-control" name="city" placeholder="Ex: Cluj-Napoca" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="address"><i class="fas fa-location-dot me-1"></i> Adresă completă</label>
                            <input type="text" class="form-control" name="address" placeholder="Ex: Str. Exemplu nr. 10" required>
                        </div>
                        <div class="form-group text-start">
                            <button type="submit" name="place_order" class="btn btn-primary px-4 py-2">
                                <i class="fab fa-cc-stripe me-2"></i>Plătește cu Card prin Stripe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>  
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<?php include('layouts/footer.php'); ?>
</body>
</html>
