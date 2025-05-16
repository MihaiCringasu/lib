

<?php include('layouts/header.php'); ?>


<!---Home-->
<section id="home">
<div class="container">
  <h5>New Arrivals</h5>
  <h1><span>Best Prices</span> this season </h1>
  <p>Charm offers the best prices</p>
  <button class="btn btn-primary">Shop now</button>
</div>
</section>

<!----brand-->
<section id="brand" class="container">
  <div class="row">
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/img/logo.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/img/logo.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/img/logo.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/img/logo.png"/>
  </div>
</section>


<!--new-->
<section id="new" class="w-100">
  <div class="row p-0 m-0">
    <!--Unu-->
    <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/promo.png"/>
      <div class="details">
        <h2>Extremely Awesome h2 new</h2>
        <button class="btn btn-primary">Shop now h2 new</button>
      </div>
    </div>
    <!--Doi-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/promo.png"/>
      <div class="details">
        <h2>Extremely Awesome h2 new</h2>
        <button class="btn btn-primary">Shop now h2 new</button>
      </div>
    </div>
    <!--trei-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/promo.png"/>
      <div class="details">
        <h2>Extremely Awesome h2 new</h2>
        <button class="btn btn-primary">Shop now h2 new</button>
      </div>
    </div>

    <!--Features-->

    <section id="featured" class="my-5 pb-5">
      <div class="container text-center mt-5 py-5">
        <h3>Our featured h3 zone</h3>
        <hr>
        <p>Here you can check out our featured products</p>
      </div>
      <div class="row mx-auto container-fluid">
        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img class="img-fluid mb-3" src="assets/img/free.png"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name">Papuci de sport h5 in featured</h5>
          <h4 class="p-price">$99</h4>
          <button class="buy-btn">Buy now </button>
        </div>
          <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img class="img-fluid mb-3" src="assets/img/free.png"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name">Papuci de sport h5 in featured</h5>
          <h4 class="p-price">$99</h4>
          <button class="buy-btn">Buy now </button>
        </div>
          <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img class="img-fluid mb-3" src="assets/img/free.png"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name">Papuci de sport h5 in featured</h5>
          <h4 class="p-price">$99</h4>
          <button class="buy-btn">Buy now </button>
        </div>
      </div>
    </section>
  </div>
</section>

<!--Banner-->
    <section id="banner" class="my-5 py-5">
      <div class="container">
        <h4> Mid season sale</h4>
        <h1> colectia de primavara <br> pana la 30%</h1>
        <button class="text-uppercase">Show now</button>
      </div>
    </section>

    <!--Haine-->

    <section id="featured" class="my-5">
      <div class="container text-center mt-5 py-5">
        <h3>Haine si ceva</h3>
        <hr>
        <p>Here you can check out our haine</p>
      </div>
      <div class="row mx-auto container-fluid">

      <?php include('server/get_featured_products.php');?>

      <?php while($row= $featured_products->fetch_assoc()) {?>

        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
          <img class="img-fluid mb-3" src="assets/img/<?php echo $row['product_image'];?>"/>
          <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
          <h4 class="p-price"><?php echo $row['product_price']; ?>/h4>
          <a href="<?php echo "single_product.php?product_id=". $row['product_id']; ?>"><button class="buy-btn">Buy now </button></a>
        </div>

        <?php } ?>
      </div>
    </section>
    
  <script src="assets/js/script.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>

<?php include('layouts/footer.php'); ?>