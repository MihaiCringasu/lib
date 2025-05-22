

<?php include('layouts/header.php'); ?>


<!---Home-->
<section id="home">
<div class="container">
  <h5>New Arrivals</h5>
  <h1><span>Best Prices</span> this season </h1>
  <p>Charm offers the best prices</p>
  <a href="shop.php" class="btn btn-primary">Shop now</a>
</div>
</section>



<!--new-->
<section id="new" class="w-100">
  <div class="row p-0 m-0">
    <!--Unu-->
    <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/coat.png"/>
      <div class="details">
        <h2>Our Coats</h2>
        <a href="shop.php?category=coats" class="btn btn-primary">Shop now</a>
      </div>
    </div>
    <!--Doi-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/ceas.png"/>
      <div class="details">
        <h2>Our Watches</h2>
        <a href="shop.php?category=watches" class="btn btn-primary">Shop now</a>
      </div>
    </div>
    <!--trei-->
        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
      <img class="img-fluid" src="assets/img/papuci.png"/>
      <div class="details">
        <h2>Our Shoes</h2>
        <a href="shop.php?category=shoes" class="btn btn-primary">Shop now</a>  
      </div>
    </div>

        <!--Haine-->

<section id="featured" class="my-5">
  <div class="container text-center mt-3 py-3">
    <h3 class="fw-bold"><i class="fas fa-fire text-danger me-2"></i>Haine și ceva</h3>
    <hr class="mx-auto w-25">
    <p>Descoperă cele mai apreciate produse din colecția noastră</p>
  </div>

  <div class="row g-4 mx-auto container-fluid">

    <?php include('server/get_featured_products.php'); ?>

    <?php while($row = $featured_products->fetch_assoc()) { ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="position-relative h-100 d-flex flex-column align-items-center p-2">

          <!-- Discount badge -->
          <?php if ($row['product_special_offer'] > 0): ?>
            <div class="discount-badge">
              <?php echo $row['product_special_offer']; ?>%
            </div>
          <?php endif; ?>

          <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>">
            <img class="img-fluid" src="assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" style="max-height: 280px; object-fit: cover;">
          </a>

          <div class="text-center mt-3">
            <h5 class="text-dark fw-bold mb-1"><?php echo $row['product_name']; ?></h5>

            <?php if ($row['product_special_offer'] > 0): 
                $original = $row['product_price'];
                $discount = $row['product_special_offer'];
                $discounted = $original * ((100 - $discount) / 100);
            ?>
              <div class="mb-2">
                <span class="text-muted text-decoration-line-through me-2">$<?php echo number_format($original, 2); ?></span>
                <span class="text-success fw-bold">$<?php echo number_format($discounted, 2); ?></span>
              </div>
            <?php else: ?>
              <h6 class="text-success mb-2">$<?php echo number_format($row['product_price'], 2); ?></h6>
            <?php endif; ?>

            <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-outline-custom">
              <i class="fas fa-shopping-cart me-1"></i> Cumpără
            </a>
          </div>

        </div>
      </div>
    <?php } ?>
  </div>
</section>


    

<!--Banner-->
    <section id="banner" class="my-5 py-5">
      <div class="container">
        <h4 style="color: #a88edb;">Mid season sale</h4>

        <h1> Reduceri de Primavara <br> pana la 30%</h1>
        <a href="shop.php" class="btn btn-primary">Shop now</a>
      </div>
    </section>

    <!-- Newsletter Subscribe -->
      <section id="newsletter" class="py-5" style="background-color: #f4f4f4;">
        <div class="container text-center">
          <h4 class="text-purple fw-bold mb-3"><i class="fas fa-envelope me-2"></i>Abonează-te la newsletter</h4>
          <p class="mb-4">Primești oferte exclusive, reduceri și noutăți direct în inbox-ul tău.</p>

          <form method="POST" action="server/newsletter.php" class="row justify-content-center">
            <div class="col-md-4">
              <input type="email" name="subscriber_email" class="form-control" placeholder="Adresa ta de email" required>
            </div>
            <div class="col-md-2">
              <button type="submit" name="subscribe" class="btn btn-primary w-100">Abonează-te</button>
            </div>
          </form>

          <?php if (isset($_GET['subscribed'])): ?>
            <div class="alert alert-success text-center mt-3">Te-ai abonat cu succes!</div>
          <?php elseif (isset($_GET['exists'])): ?>
            <div class="alert alert-warning text-center mt-3">Acest email este deja abonat.</div>
          <?php elseif (isset($_GET['invalid'])): ?>
            <div class="alert alert-danger text-center mt-3">Adresă de email invalidă.</div>
          <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center mt-3">A apărut o eroare. Încearcă din nou.</div>
          <?php endif; ?>

          <?php if (isset($_GET['subscribed']) || isset($_GET['exists']) || isset($_GET['invalid']) || isset($_GET['error'])): ?>
            <script>
              if (window.history.replaceState) {
                const cleanURL = window.location.href.split("?")[0];
                window.history.replaceState(null, null, cleanURL);
              }
            </script>
          <?php endif; ?>

        </div>
      </section>



  <script src="assets/js/script.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>


<?php include('layouts/footer.php'); ?>