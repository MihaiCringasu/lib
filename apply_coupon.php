    <?php
    session_start();
    include('server/connection.php');

    $response = ['success' => false, 'message' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_code'])) {
        $code = strtoupper(trim($_POST['discount_code']));

        $stmt = $conn->prepare("SELECT discount_percent, expires_at FROM coupons WHERE code = ? LIMIT 1");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $coupon = $result->fetch_assoc();

            $expires = $coupon['expires_at'];
            $discount = $coupon['discount_percent'];

            if (strtotime($expires) >= time()) {
                $_SESSION['discount_applied'] = true;
                $_SESSION['discount_code'] = $code;
                $_SESSION['discount_percent'] = $discount;

                $response['success'] = true;
                $response['message'] = "Reducerea de $discount% a fost aplicată!";
            } else {
                $response['message'] = "Cuponul a expirat.";
            }
        } else {
            $response['message'] = "Cod invalid.";
        }
        $stmt->close();
    } else {
        $response['message'] = "Cerere invalidă.";
    }

    header('Content-Type: application/json');
    echo json_encode($response);
