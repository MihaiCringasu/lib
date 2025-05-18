    <?php
    session_start();
    require 'vendor/autoload.php';
    require 'server/connection.php'; // make sure this is the correct path to your DB connection

    $stripeConfig = include('stripe.php');
    \Stripe\Stripe::setApiKey($stripeConfig['secret_key']);


    if (isset($_GET['session_id'])) {
        $session_id = $_GET['session_id'];

        try {
            $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($checkout_session->payment_status === 'paid') {
                // Retrieve order ID from metadata
                $order_id = $checkout_session->metadata->order_id;

                // Update the order status in your database
                $stmt = $conn->prepare("UPDATE orders SET order_status = 'Paid' WHERE order_id = ?");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
            }

        } catch (Exception $e) {
            echo "Error retrieving session: " . $e->getMessage();
            exit;
        }

    } else {
        echo "No session ID provided!";
        exit;
    }

    session_destroy();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Plată reușită</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container text-center mt-5">
            <h1 class="text-success"> Plata a fost efectuată cu succes!</h1>
            <p>Îți mulțumim pentru comandă.</p>
            <a href="index.php" class="btn btn-primary mt-3">Înapoi la magazin</a>
        </div>
    </body>
    </html>
