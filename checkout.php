<?php
session_start();
include('server/connection.php');


// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('location: index.php');
    exit;
}

// Include Stripe SDK
require 'vendor/autoload.php'; // Make sure this path is correct
$stripeConfig = include('stripe.php');
\Stripe\Stripe::setApiKey($stripeConfig['secret_key']);

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    // Store user data in session for later
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


    // Create Stripe Checkout Session
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Your Cart Total',
                ],
                'unit_amount' => $_SESSION['total'] * 100, // Stripe uses cents
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

    // Redirect to Stripe Checkout
    header("Location: " . $session->url);
    exit;
}
?>

<?php include('layouts/header.php'); ?>

<!--Checkout-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Checkout</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="checkout-form" method="POST" action="">
            <p class="text-center" style="color: red;"><?php if (isset($_GET['message'])) echo $_GET['message']; ?>
                <?php if (isset($_GET['message'])) { ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                <?php } ?>
            </p>

            <div class="form-group checkout-small-element">
                <label>Name</label>
                <input type="text" class="form-control" name="name" placeholder="name" required/>
            </div>
            <div class="form-group checkout-small-element">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="email" required/>
            </div>
            <div class="form-group checkout-small-element">
                <label>Phone</label>
                <input type="tel" class="form-control" name="phone" placeholder="phone" required/>
            </div>
            <div class="form-group checkout-small-element">
                <label>City</label>
                <input type="text" class="form-control" name="city" placeholder="City" required/>
            </div>
            <div class="form-group checkout-large-element">
                <label>Address</label>
                <input type="text" class="form-control" name="address" placeholder="Address" required/>
            </div>
            <div class="form-group checkout-btn-container">
                <p>Total amount: $ <?php echo $_SESSION['total']; ?></p>
                <input type="submit" class="btn btn-primary" name="place_order" value="Pay with Card via Stripe"/>
            </div>
        </form>
    </div>
</section>

<script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<?php include('layouts/footer.php'); ?>
</body>
</html>
