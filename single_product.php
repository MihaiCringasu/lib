<?php
session_start();
include('server/connection.php');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Get product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result();
    $product_data = $product->fetch_assoc();

    // Get related products
    $related_stmt = $conn->prepare("SELECT * FROM products WHERE product_category = ? AND product_id != ? LIMIT 4");
    $related_stmt->bind_param("si", $product_data['product_category'], $product_id);
    $related_stmt->execute();
    $related_products = $related_stmt->get_result();

    // Handle comment submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $comment_text = trim($_POST['comment']);
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

        if (!empty($comment_text)) {
            $insert_comment = $conn->prepare("INSERT INTO comments (product_id, user_id, comment_text, rating) VALUES (?, ?, ?, ?)");
            $insert_comment->bind_param("iisi", $product_id, $user_id, $comment_text, $rating);
            $insert_comment->execute();
        }

        header("Location: single_product.php?product_id=$product_id");
        exit();
    }


    // Fetch comments (with profile image + ID)
    $comment_stmt = $conn->prepare("
        SELECT comments.comment_id, comments.comment_text, comments.created_at, comments.rating, users.user_name, users.user_id, users.user_image 
        FROM comments 
        JOIN users ON comments.user_id = users.user_id 
        WHERE comments.product_id = ? 
        ORDER BY comments.created_at DESC
    ");
    $comment_stmt->bind_param("i", $product_id);
    $comment_stmt->execute();
    $comment_results = $comment_stmt->get_result();

} else {
    header('location:index.php');
    exit();
}

    // Media ratingurilor
    $rating_query = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM comments WHERE product_id = ?");
    $rating_query->bind_param("i", $product_id);
    $rating_query->execute();
    $rating_result = $rating_query->get_result()->fetch_assoc();

    $average_rating = round($rating_result['avg_rating'], 1); // de ex. 4.2
    $total_reviews = $rating_result['total_reviews'];

?>

<?php include('layouts/header.php'); ?>

<!-- Product Section -->
<section class="container single-product my-4 pt-4">
    <div class="row mt-5">

        <div class="col-lg-5 col-md-6 col-sm-12">
            <img class="img-fluid w-100 pb-1" src="assets/img/<?php echo $product_data['product_image']; ?>" id="mainImg"/>
            <div class="small-img-group">
                <div class="small-img-col">
                    <img src="assets/img/<?php echo $product_data['product_image']; ?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/img/<?php echo $product_data['product_image2']; ?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/img/<?php echo $product_data['product_image3']; ?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/img/<?php echo $product_data['product_image4']; ?>" width="100%" class="small-img"/>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">
            <h6><?php echo ucfirst($product_data['product_category']); ?></h6>

            <h3 class="py-4 d-flex align-items-center gap-3">
                <?php echo $product_data['product_name']; ?>
                <?php if ($total_reviews > 0): ?>
                    <span class="text-warning small">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= round($average_rating) ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                        <span class="text-dark">(<?= $average_rating ?>/5 din <?= $total_reviews ?> review<?= $total_reviews == 1 ? '' : 'uri' ?>)</span>
                    </span>
                <?php else: ?>
                    <span class="text-muted small">(Nicio recenzie încă)</span>
                <?php endif; ?>
            </h3>
            
            <?php if ($product_data['product_special_offer'] > 0): 
                    $original_price = $product_data['product_price'];
                    $discount = $product_data['product_special_offer'];
                    $discounted_price = $original_price * ((100 - $discount) / 100);
                 ?>
                    <h4>
                        <span class="text-danger me-2">
                            <i class="fas fa-tag"></i> <?php echo $discount; ?>% reducere
                        </span>
                    </h4>
                    <h2>
                        <span class="text-muted text-decoration-line-through me-2">$<?php echo number_format($original_price, 2); ?></span>
                        <span class="text-success">$<?php echo number_format($discounted_price, 2); ?></span>
                    </h2>
                <?php else: ?>
                    <h2>$<?php echo number_format($product_data['product_price'], 2); ?></h2>
                <?php endif; ?>

            <form method="POST" action="cart.php" class="mb-2">
                
                    <form method="POST" action="cart.php" class="mb-2">
                    <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>"/>
                    <input type="hidden" name="product_image" value="<?php echo $product_data['product_image']; ?>"/>
                    <input type="hidden" name="product_name" value="<?php echo $product_data['product_name']; ?>"/>   
                    <?php
                        $price_for_cart = $product_data['product_special_offer'] > 0 
                            ? $product_data['product_price'] * ((100 - $product_data['product_special_offer']) / 100) 
                            : $product_data['product_price'];
                    ?>
                    <input type="hidden" name="product_price" value="<?php echo number_format($price_for_cart, 2, '.', ''); ?>"/>           
                    <input type="number" name="product_quantity" value="1"/>
                    <button class="btn buy-btn" type="submit" name="add_to_cart">Add to cart</button>
                </form>

                <!-- Formular separat pentru Wishlist -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="wishlist.php">
                    <input type="hidden" name="product_id" value="<?= $product_data['product_id']; ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product_data['product_name']); ?>">
                    <button type="submit" name="add_to_wishlist" class="btn btn-outline-secondary">
                        <i class="fas fa-heart me-1"></i> Adaugă la Wishlist
                    </button>
                </form>
                <?php endif; ?>


            </form>
            <h4 class="mt-5 mb-5">Product details</h4>
            <span><?php echo $product_data['product_description']; ?></span>
        </div>     
    </div>
</section>

<!-- Related Products -->
<section id="related-products" class="my-5">
  <div class="container text-center mt-5 py-4">
    <h3 class="fw-bold"><i class="fas fa-thumbs-up me-2 text-purple"></i>Produse recomandate</h3>
    <hr class="mx-auto w-25">
    <p>Inspiră-te din selecția noastră de produse similare</p>
  </div>

  <div class="row g-4 mx-auto container-fluid">
    <?php while($related = $related_products->fetch_assoc()) { ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="product-card shadow-sm position-relative h-100">

          <!-- Discount badge -->
          <?php if ($related['product_special_offer'] > 0): ?>
            <div class="discount-badge">
              <?php echo $related['product_special_offer']; ?>%
            </div>
          <?php endif; ?>

          <a href="single_product.php?product_id=<?php echo $related['product_id']; ?>">
            <img class="img-fluid" src="assets/img/<?php echo $related['product_image']; ?>" alt="<?php echo htmlspecialchars($related['product_name']); ?>">
          </a>

          <div class="product-details">
            <h5 class="p-name text-dark fw-bold mb-1"><?php echo $related['product_name']; ?></h5>

            <?php if ($related['product_special_offer'] > 0): 
                $original = $related['product_price'];
                $discount = $related['product_special_offer'];
                $discounted = $original * ((100 - $discount) / 100);
            ?>
              <div class="mb-2">
                <span class="text-muted text-decoration-line-through me-2">$<?php echo number_format($original, 2); ?></span>
                <span class="text-success fw-bold">$<?php echo number_format($discounted, 2); ?></span>
              </div>
            <?php else: ?>
              <h6 class="p-price text-success mb-2">$<?php echo number_format($related['product_price'], 2); ?></h6>
            <?php endif; ?>

            <a href="single_product.php?product_id=<?php echo $related['product_id']; ?>" class="btn btn-outline-custom">
              <i class="fas fa-shopping-cart me-1"></i> Cumpără
            </a>
          </div>

        </div>
      </div>
    <?php } ?>
  </div>
</section>

<!-- Comments Section -->
<section class="container my-5" id="comments">
    <h3>Comentarii</h3>
    <hr>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" class="mb-4">
            <textarea name="comment" class="form-control mb-2" placeholder="Scrie un comentariu..." required></textarea>
            <div class="mb-2">
            <label class="form-label">Acordă un rating:</label>
            <div class="star-rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                <?php endfor; ?>
            </div>
            </div>
            <button type="submit" class="btn btn-primary">Trimite</button>

            <style>
            .star-rating {
                direction: rtl;
                display: inline-flex;
                gap: 5px;
            }
            .star-rating input[type="radio"] {
                display: none;
            }
            .star-rating label {
                font-size: 1.5rem;
                color: #ccc;
                cursor: pointer;
            }
            .star-rating input[type="radio"]:checked ~ label {
                color: #ffc107;
            }
            </style>

        </form>
    <?php else: ?>
        <p>Trebuie să fii logat pentru a lăsa un comentariu.</p>
    <?php endif; ?>

    <div class="comment-list">
        <?php while($c = $comment_results->fetch_assoc()) { ?>
            <div class="comment bg-light p-3 mb-3 rounded d-flex">
                <img src="assets/img/<?php echo htmlspecialchars($c['user_image'] ?? 'default.png'); ?>" width="50" height="50" class="rounded-circle me-3" alt="User">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <strong><?= htmlspecialchars($c['user_name']) ?></strong>
                        <?php if (isset($c['rating'])): ?>
                            <span class="text-warning small">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= (int)$c['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="text-muted small d-block"><?= $c['created_at'] ?></span>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $c['user_id']) : ?>
                        <form method="POST" action="delete_comment.php" onsubmit="return confirm('Ștergi comentariul?')">
                            <input type="hidden" name="comment_id" value="<?= $c['comment_id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                            <button type="submit" class="btn btn-sm btn-danger mt-2">Șterge</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>  
    </div>

</section>

<!-- JS for Image Switching -->
<script>
    var mainImg = document.getElementById("mainImg");
    var smallImg = document.getElementsByClassName("small-img");

    for(let i = 0; i < smallImg.length; i++) {
        smallImg[i].onclick = function() {
            mainImg.src = smallImg[i].src;
        }
    }
</script>

<?php include('layouts/footer.php'); ?>