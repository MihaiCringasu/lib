<?php
session_start();
include('server/connection.php');

// Inițializează wishlist dacă nu există
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// ✅ Adaugă produs în wishlist (nou)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $product_name = $_POST['product_name'] ?? '';

    $already_in_wishlist = false;

    // Căutăm produsul în wishlist
    foreach ($_SESSION['wishlist'] as $item) {
        if ($item['product_id'] == $product_id) {
            $already_in_wishlist = true;
            break;
        }
    }

    // Dacă nu e deja, îl adăugăm
    if (!$already_in_wishlist) {
        $_SESSION['wishlist'][] = [
            'product_id' => $product_id,
            'product_name' => $product_name
        ];
    }

    header("Location: wishlist.php");
    exit();
}

// Șterge un produs din wishlist
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($item) {
        return $item['product_id'] != $_GET['remove'];
    });
    header('Location: wishlist.php');
    exit();
}

// Când se trimit produse selectate spre cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_selected'])) {
    foreach ($_POST['wishlist_selected'] as $product_id) {
        $product_id = (int)$product_id;

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $price = $row['product_special_offer'] > 0
                ? $row['product_price'] * ((100 - $row['product_special_offer']) / 100)
                : $row['product_price'];

            $_SESSION['cart'][$product_id] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'product_image' => $row['product_image'],
                'product_price' => number_format($price, 2, '.', ''),
                'product_quantity' => 1
            ];
        }
    }

    // Stergem produsele adaugate din wishlist
    $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($item) {
        return !in_array($item['product_id'], $_POST['wishlist_selected']);
    });

    // Calculăm totalul coșului
    function calculateTotalCart() {
        $total = 0;
        $total_quantity = 0;
        foreach ($_SESSION['cart'] as $product) {
            $total += $product['product_price'] * $product['product_quantity'];
            $total_quantity += $product['product_quantity'];
        }
        $_SESSION['total'] = $total;
        $_SESSION['quantity'] = $total_quantity;
    }
    calculateTotalCart();

    header('Location: cart.php');
    exit();
}

// Obține produse wishlist pentru afișare
$wishlist_ids = array_column($_SESSION['wishlist'], 'product_id');
$products = [];
if (!empty($wishlist_ids)) {
    $placeholders = implode(',', array_fill(0, count($wishlist_ids), '?'));
    $types = str_repeat('i', count($wishlist_ids));
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->bind_param($types, ...$wishlist_ids);
    $stmt->execute();
    $products = $stmt->get_result();
}
?>


<?php include('layouts/header.php'); ?>

<div class="container mt-5 py-5">
  <h3 class="mb-4 text-center"><i class="fas fa-heart text-danger me-2"></i>Wishlist-ul tău</h3>

  <?php if (!empty($_SESSION['wishlist']) && $products->num_rows > 0): ?>
    <form method="POST" class="mx-auto" style="max-width: 600px;">
      <div class="vstack gap-3">
        <?php while ($product = $products->fetch_assoc()): ?>
          <div class="card shadow-sm p-2 small d-flex flex-row align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <input class="form-check-input me-2" type="checkbox" name="wishlist_selected[]" value="<?= $product['product_id'] ?>">
              <img src="assets/img/<?= $product['product_image'] ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="<?= htmlspecialchars($product['product_name']) ?>">
              <div>
                <strong class="d-block small mb-1"><?= htmlspecialchars($product['product_name']) ?></strong>

                <?php if ($product['product_special_offer'] > 0): 
                    $original = $product['product_price'];
                    $discount = $product['product_special_offer'];
                    $discounted = $original * ((100 - $discount) / 100);
                ?>
                  <div>
                    <small class="text-muted text-decoration-line-through">$<?= number_format($original, 2) ?></small>
                    <span class="text-success fw-bold ms-2">$<?= number_format($discounted, 2) ?></span>
                  </div>
                <?php else: ?>
                  <div class="text-success fw-bold">$<?= number_format($product['product_price'], 2) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <a href="wishlist.php?remove=<?= $product['product_id'] ?>" class="btn btn-sm btn-outline-danger ms-3"><i class="fas fa-times"></i></a>
          </div>
        <?php endwhile; ?>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-primary mt-4 px-4" name="add_selected_to_cart">
          <i class="fas fa-shopping-cart me-1"></i> Adaugă în coș
        </button>
      </div>
    </form>
  <?php else: ?>
    <div class="alert alert-info text-center">Nu ai produse în wishlist.</div>
  <?php endif; ?>
</div>

<?php include('layouts/footer.php'); ?>
