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

        if (!empty($comment_text)) {
            $insert_comment = $conn->prepare("INSERT INTO comments (product_id, user_id, comment_text) VALUES (?, ?, ?)");
            $insert_comment->bind_param("iis", $product_id, $user_id, $comment_text);
            $insert_comment->execute();
        }

        // Redirect to avoid resubmission
        header("Location: single_product.php?product_id=$product_id");
        exit();
    }

    // Fetch comments (with profile image + ID)
    $comment_stmt = $conn->prepare("
        SELECT comments.comment_id, comments.comment_text, comments.created_at, users.user_name, users.user_id, users.user_image 
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
?>

<?php include('layouts/header.php'); ?>

<!-- Product Section -->
<section class="container single-product my-5 pt-5">
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
            <h3 class="py-4"><?php echo $product_data['product_name']; ?></h3>
            <h2>$<?php echo $product_data['product_price']; ?></h2>

            <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>"/>
                <input type="hidden" name="product_image" value="<?php echo $product_data['product_image']; ?>"/>
                <input type="hidden" name="product_name" value="<?php echo $product_data['product_name']; ?>"/>   
                <input type="hidden" name="product_price" value="<?php echo $product_data['product_price']; ?>"/>           
                <input type="number" name="product_quantity" value="1"/>
                <button class="buy-btn" type="submit" name="add_to_cart">Add to cart</button>
            </form>
            <h4 class="mt-5 mb-5">Product details</h4>
            <span><?php echo $product_data['product_description']; ?></span>
        </div>     
    </div>
</section>

<!-- Related Products -->
<section id="related-products" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Related products</h3>
        <hr>
        <p>Here you can check out our related items</p>
    </div>
    <div class="row mx-auto container-fluid related-products-container">
        <?php while($related = $related_products->fetch_assoc()) { ?>
            <div class="product text-center col-lg-3 col-md-4 col-sm-12 related-product">
                <img class="img-fluid mb-3" src="assets/img/<?php echo $related['product_image']; ?>"/>
                <div class="star">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <h5 class="p-name"><?php echo $related['product_name']; ?></h5>
                <h4 class="p-price">$<?php echo $related['product_price']; ?></h4>
                <a href="single_product.php?product_id=<?php echo $related['product_id']; ?>" class="btn buy-btn">Buy now</a>
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
            <button type="submit" class="btn btn-primary">Trimite</button>
        </form>
    <?php else: ?>
        <p>Trebuie să fii logat pentru a lăsa un comentariu.</p>
    <?php endif; ?>

    <div class="comment-list">
        <?php while($c = $comment_results->fetch_assoc()) { ?>
            <div class="comment bg-light p-3 mb-3 rounded d-flex">
                <img src="assets/img/<?php echo htmlspecialchars($c['user_image'] ?? 'default.png'); ?>" width="50" height="50" class="rounded-circle me-3" alt="User">
                <div>
                    <strong><?= htmlspecialchars($c['user_name']) ?></strong>
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
