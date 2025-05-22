<?php

session_start();
include('server/connection.php');

// 1. Setăm pagina curentă
$page_no = isset($_GET['page_no']) && $_GET['page_no'] != "" ? (int)$_GET['page_no'] : 1;

// 2. Preluăm filtrele din URL (GET)
$category = $_GET['category'] ?? '';
$price = isset($_GET['price']) ? (int)$_GET['price'] : 1000;

// 3. Calculăm numărul total de produse
if ($category != '') {
    $stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM products WHERE product_category = ? AND product_price <= ?");
    $stmt1->bind_param("si", $category, $price);
} else {
    $stmt1 = $conn->prepare("SELECT COUNT(*) AS total_records FROM products WHERE product_price <= ?");
    $stmt1->bind_param("i", $price);
}
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();

// 4. Setări pentru paginare
$total_records_per_page = 9;
$offset = ($page_no - 1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// 5. Selectăm produsele pentru afișare
if ($category != '') {
    $stmt2 = $conn->prepare("SELECT * FROM products WHERE product_category = ? AND product_price <= ? LIMIT ?, ?");
    $stmt2->bind_param("siii", $category, $price, $offset, $total_records_per_page);
} else {
    $stmt2 = $conn->prepare("SELECT * FROM products WHERE product_price <= ? LIMIT ?, ?");
    $stmt2->bind_param("iii", $price, $offset, $total_records_per_page);
}
$stmt2->execute();
$products = $stmt2->get_result();
?>

<?php include('layouts/header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
  body {
    background-color: #d8dde3;
  }
  .product-card {
    background-color: #f4f7fa;
    border-radius: 0 0 10px 10px;
    overflow: hidden;
    transition: 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .product-card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 0;
  }
  .product-details {
    background-color: #f4f7fa;
    padding: 15px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 160px;
  }
  .product-details .btn {
    margin-top: auto;
    align-self: center;
  }
  .search-sidebar {
    background-color: #f0f3f7;
    border-radius: 8px;
  }
  @media (max-width: 768px) {
    .product-card img {
      height: 200px;
    }
  }
</style>

<!-- Shop Section -->
<section id="search" class="my-5 py-5">
  <div class="container mt-5 py-5">
    <div class="row">

      <!-- Sidebar Filters -->
      <div class="col-lg-3 mb-4">
        <div class="search-sidebar p-3 shadow-sm">
          <h5><i class="fas fa-filter me-2"></i>Filtrare produse</h5>
          <hr>

          <form action="shop.php" method="GET">
            <p><strong><i class="fas fa-tags me-2"></i>Categorie</strong></p>
            <div class="form-check">
              <input class="form-check-input" value="shoes" type="radio" name="category" id="category_one" <?php if(isset($category)&& $category == 'shoes'){ echo 'checked';}?>>
              <label class="form-check-label" for="category_one"> Shoes</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="coats" type="radio" name="category" id="category_two" <?php if(isset($category)&& $category =='coats'){ echo 'checked';}?>>
              <label class="form-check-label" for="category_two">Coats</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="watches" type="radio" name="category" id="category_three" <?php if(isset($category)&& $category =='watches'){ echo 'checked';}?>>
              <label class="form-check-label" for="category_three">Watches</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="bags" type="radio" name="category" id="category_four" <?php if(isset($category)&& $category =='bags'){ echo 'checked';}?>>
              <label class="form-check-label" for="category_four">Bags</label>
            </div>

            <p class="mt-4"><strong><i class="fas fa-dollar-sign me-2"></i>Preț</strong></p>
            <input type="range" class="form-range" name="price" id="priceRange" value="<?php echo isset($price) ? $price : 1000; ?>" min="1" max="1000" oninput="priceOutput.value = priceRange.value">
            <div class="d-flex justify-content-between">
              <span>1</span>
              <output id="priceOutput"><?php echo isset($price) ? $price : 1000; ?></output>
            </div>

            <div class="form-group my-3">
              <button type="submit" name="search" class="btn btn-dark w-100"><i class="fas fa-search"></i> Caută</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Products List -->
      <div class="col-lg-9">
        <h3 class="mb-3"><i class="fas fa-boxes me-2"></i>Produsele noastre</h3>
        <hr>

        <div class="row g-4">
          <?php while($row = $products->fetch_assoc()) { ?>
            <div class="col-lg-4 col-md-6">
              <div class="product-card shadow-sm">
                <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>">
                  <img src="assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                </a>
                <div class="product-details">
                  <h5 class="p-name text-dark fw-bold mb-1"><?php echo $row['product_name']; ?></h5>
                  <h6 class="p-price text-success mb-2">$<?php echo $row['product_price']; ?></h6>
                  <a class="btn btn-outline-primary btn-sm" href="single_product.php?product_id=<?php echo $row['product_id']; ?>">
                    <i class="fas fa-shopping-cart me-1"></i> Cumpără
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">
            <li class="page-item <?php if($page_no<=1){echo'disabled';}?>">
              <a class="page-link" href="<?php if($page_no <= 1){ echo '#';} else{ echo"?page_no=".($page_no-1);} ?>">&laquo; Înapoi</a>
            </li>
            <?php for ($i = 1; $i <= $total_no_of_pages; $i++): ?>
              <li class="page-item <?php if($page_no==$i) echo 'active'; ?>">
                <a class="page-link" href="?page_no=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php if($page_no >= $total_no_of_pages){ echo 'disabled'; }?>">
              <a class="page-link" href="<?php if($page_no >= $total_no_of_pages){ echo '#';}else{ echo "?page_no=".($page_no+1);} ?>">Înainte &raquo;</a>
            </li>
          </ul>
        </nav>

      </div>

    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<?php include('layouts/footer.php'); ?>
</body>
</html>
