<?php

include('server/connection.php');

if(isset($_GET['product_id'])){

  $product_id = $_GET['product_id'];

  
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i",$product_id);
    $stmt->execute();

     $product = $stmt->get_result();





  //no product id was given
}else{

  header('location:index.php');
} 




?>

<?php include('layouts/header.php'); ?>


    <!--Imaginea produsului-->
    <section class="container single-product my-5 pt-5">
        <div class="row mt-5">

          <?php   while($row = $product->fetch_assoc()) { ?>
         
            <div class="col-lg-5 col-md-6 col-sm-12">
                <img class="img-fluid w-100 pb-1" src="assets/img/<?php echo $row['product_image']; ?>" id="mainImg"/>
                <div class="small-img-group">
                    <div class="small-img-col">
                        <img src="assets/img/<?php echo $row['product_image']; ?>" width="100%" class="small-img"/>
                    </div>
                      <div class="small-img-col">
                        <img src="assets/img/<?php echo $row['product_image2']; ?>" width="100%" class="small-img"/>
                    </div>
                      <div class="small-img-col">
                        <img src="assets/img/<?php echo $row['product_image3']; ?>" width="100%" class="small-img"/>
                    </div>
                      <div class="small-img-col">
                        <img src="assets/img/<?php echo $row['product_image4']; ?>" width="100%" class="small-img"/>
                    </div>
                </div>
            </div>

            


           <div class="col-lg-6 col-md-12 col-sm-12">
            <h6>Men/Shoes</h6>
            <h3 class="py-4"><?php echo $row['product_name']; ?></h3>
            <h2>$<?php echo $row['product_price']; ?></h2>

             <form method="POST" action="cart.php">
             <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>"/>
             <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>"/>
             <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>"/>   
             <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>"/>           

            <input type="number" name="product_quantity" value="1"/>
            <button class="buy-btn" type="submit" name="add_to_cart">Add to cart</button>
            </form>
            <h4 class="mt-5 mb-5"> Products details</h4>
            <span><?php echo $row['product_description']; ?></span>
           </div>     
          
            <?php } ?>

        </div>
    </section>


    <!--related products-->
        <section id="related-products" class="my-5">
      <div class="container text-center mt-5 py-5">
        <h3>Related products</h3>
        <hr>
        <p>Here you can check out our haine</p>
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






<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>*/-->

    <script>
        var mainImg = document.getElementById("mainImg"); /*lam pus la single products idu*/
        var smallImg = document.getElementsByClassName("small-img");

        for(let i=0; i<4; i++){
            smallImg[i].onclick = function(){
            mainImg.src = smallImg[i].src;
        }
    }

    </script>
