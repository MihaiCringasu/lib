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
$total_records_per_page = 8;
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
<style>
  body {
    background-color: #b8b9d1 ;
  }
</style>


    <!--featured search-->
    <section id="search" class="my-5 py-5 ms-2">
      <div class="container mt-5 py-5">
    <div class="row">
      
      <!-- Sidebar Search Filters -->
      <div class="col-lg-3">
        <div class="search-sidebar">
          <h5>Search products</h5>
          <hr>

          <form action="shop.php" method="GET">
            <p>Category</p>
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

            <p class="mt-4">Price</p>
            <input type="range" class="form-range" name="price" id="priceRange" value="<?php echo isset($price) ? $price : 1000; ?>" min="1" max="1000" oninput="priceOutput.value = priceRange.value">

            <div class="price-range d-flex justify-content-between w-100 mt-2">
              <span>1</span>
              <output id="priceOutput"><?php echo isset($price) ? $price : 1000; ?></output>
            </div>

            <div class="form-group my-3">
              <input type="submit" name="search" value="Search" class="btn btn-dark w-100">
            </div>
          </form>
        </div>
      </div>

      <!-- Products -->
      <div class="col-lg-9">
        <h3>Our Products</h3>
        <hr>
        <p>Here you can check out our products</p>

        <div class="row">
          <?php while($row = $products->fetch_assoc()) { ?>
            <div class="product text-center col-lg-4 col-md-6 col-sm-12 mb-4" style="cursor: pointer;">
              <div onclick="window.location.href='single_product.php?product_id=<?php echo $row['product_id']; ?>'">
                <img class="img-fluid mb-3" src="assets/img/<?php echo $row['product_image']; ?>"/>
                <div class="star">
                  <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                  <i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
              </div>
              <a class="btn shop-buy-btn" href="single_product.php?product_id=<?php echo $row['product_id']; ?>">Buy now</a>
            </div>
          <?php } ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">

            <li class="page-item <?php if($page_no<=1){echo'disabled';}?>">
              <a class="page-link" href="<?php if($page_no <= 1){ echo '#';} else{ echo"?page_no=".$page_no-1;} ?>" >Previous</a>
            </li>
            <li class="page-item"><a class="page-link" href="?page_no=1">1</a></li>
            <li class="page-item"><a class="page-link" href="?page_no=2">2</a></li>

            <?php if( $page_no >=3 ) {?>
            <li class="page-item"><a class="page-link" href="#">...</a></li>
            <li class="page-item"><a class="page-link" href="<?php echo "?page_no=".$page_no;?>"><?php echo $page_no;?></a></li>
            <?php } ?>



            <li class="page-item <?php if($page_no >= $total_no_of_pages){ echo 'disabled'; }?>">
              <a class="page-link" href="<?php if($page_no >= $total_no_of_pages){ echo '#';}else{ echo "?page_no=".($page_no+1);} ?>" >Next</a>
            </li>
          </ul>
        </nav>

      </div>

    </div>
  </div>
</section>  




<!--de inclus footer si aici si in home-->


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
